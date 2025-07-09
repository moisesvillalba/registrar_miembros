<?php
/**
 * EDITAR REGISTRO DE MIEMBRO - Sistema de Registro
 * Archivo: editar_registro.php
 * Descripción: Página para editar los datos de un miembro registrado
 */

// ============================================================================
// 1. INICIALIZACIÓN Y SEGURIDAD
// ============================================================================

// Iniciar sesión para manejar la autenticación
session_start();

// Verificar que el usuario esté autenticado y sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'includes/flash.php';

// ============================================================================
// 2. VALIDACIÓN DE PARÁMETROS
// ============================================================================

// Verificar que se haya proporcionado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    if (function_exists('setFlashMessage')) {
        setFlashMessage('error', 'ID de registro no válido');
    }
    header("Location: list.php");
    exit();
}

$id_miembro = (int)$_GET['id'];

// ============================================================================
// 3. CONEXIÓN A LA BASE DE DATOS
// ============================================================================

try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// ============================================================================
// 4. OBTENER DATOS DEL MIEMBRO
// ============================================================================

try {
    $sql = "SELECT * FROM miembros WHERE id = :id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id_miembro, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        if (function_exists('setFlashMessage')) {
            setFlashMessage('error', 'El registro solicitado no existe');
        }
        header("Location: list.php");
        exit();
    }
    
    $miembro = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    if (function_exists('setFlashMessage')) {
        setFlashMessage('error', 'Error al cargar el registro');
    }
    header("Location: list.php");
    exit();
}

// ============================================================================
// 5. PROCESAMIENTO DEL FORMULARIO
// ============================================================================

$errores = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener y limpiar datos del formulario
    $ci = trim($_POST['ci'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $sexo = trim($_POST['sexo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $barrio = trim($_POST['barrio'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $institucion_actual = trim($_POST['institucion_actual'] ?? '');
    $nivel_actual = trim($_POST['nivel_actual'] ?? '');
    $turno = trim($_POST['turno'] ?? '');
    $ano_estudio = trim($_POST['ano_estudio'] ?? '');
    $observaciones = trim($_POST['observaciones'] ?? '');
    
    // Verificar qué columnas existen en la tabla
    $stmt_columns = $db->query("DESCRIBE miembros");
    $existing_columns = [];
    while ($row = $stmt_columns->fetch(PDO::FETCH_ASSOC)) {
        $existing_columns[] = $row['Field'];
    }
    
    // ===== VALIDACIONES =====
    
    // CI es obligatorio y debe ser único (excepto para el registro actual)
    if (empty($ci)) {
        $errores[] = "El CI es obligatorio";
    } else {
        // Verificar que el CI no esté duplicado
        $sql_check = "SELECT id FROM miembros WHERE ci = :ci AND id != :id_actual";
        $stmt_check = $db->prepare($sql_check);
        $stmt_check->bindParam(':ci', $ci);
        $stmt_check->bindParam(':id_actual', $id_miembro, PDO::PARAM_INT);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            $errores[] = "Ya existe un miembro registrado con este CI";
        }
    }
    
    // Nombre y apellido son obligatorios
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio";
    }
    
    if (empty($apellido)) {
        $errores[] = "El apellido es obligatorio";
    }
    
    // Validar email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido";
    }
    
    // Validar fecha de nacimiento si se proporciona
    if (!empty($fecha_nacimiento)) {
        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
        if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha_nacimiento) {
            $errores[] = "El formato de la fecha de nacimiento no es válido";
        } else {
            // Verificar que la fecha no sea futura
            $hoy = new DateTime();
            if ($fecha_obj > $hoy) {
                $errores[] = "La fecha de nacimiento no puede ser futura";
            }
        }
    }
    
    // ===== ACTUALIZAR SI NO HAY ERRORES =====
    
    if (empty($errores)) {
        try {
            // Construir la consulta SQL dinámicamente basada en las columnas existentes
            $fields_to_update = [
                'ci' => $ci,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'fecha_nacimiento' => $fecha_nacimiento ?: null,
                'sexo' => $sexo ?: null,
                'telefono' => $telefono ?: null,
                'email' => $email ?: null,
                'direccion' => $direccion ?: null,
                'barrio' => $barrio ?: null,
                'ciudad' => $ciudad ?: null,
                'institucion_actual' => $institucion_actual ?: null,
                'nivel_actual' => $nivel_actual ?: null,
                'turno' => $turno ?: null,
                'ano_estudio' => $ano_estudio ?: null
            ];
            
            // Agregar campos opcionales solo si existen en la tabla
            if (in_array('observaciones', $existing_columns)) {
                $fields_to_update['observaciones'] = $observaciones ?: null;
            }
            
            // Construir la consulta SQL
            $set_clauses = [];
            foreach ($fields_to_update as $field => $value) {
                if (in_array($field, $existing_columns)) {
                    $set_clauses[] = "$field = :$field";
                }
            }
            
            // Agregar fecha de actualización si la columna existe
            if (in_array('fecha_actualizacion', $existing_columns)) {
                $set_clauses[] = "fecha_actualizacion = NOW()";
            }
            
            $sql_update = "UPDATE miembros SET " . implode(', ', $set_clauses) . " WHERE id = :id";
            
            $stmt_update = $db->prepare($sql_update);
            
            // Vincular parámetros
            foreach ($fields_to_update as $field => $value) {
                if (in_array($field, $existing_columns)) {
                    $stmt_update->bindParam(":$field", $fields_to_update[$field]);
                }
            }
            $stmt_update->bindParam(':id', $id_miembro, PDO::PARAM_INT);
            
            if ($stmt_update->execute()) {
                if (function_exists('setFlashMessage')) {
                    setFlashMessage('success', 'Registro actualizado correctamente');
                }
                header("Location: ver_registro.php?id=" . $id_miembro);
                exit();
            } else {
                $errores[] = "Error al actualizar el registro";
            }
            
        } catch (PDOException $e) {
            $errores[] = "Error de base de datos: " . $e->getMessage();
        }
    }
    
    // Si llegamos aquí y hay errores, actualizar los datos del miembro con los valores del formulario
    if (!empty($errores)) {
        $miembro = array_merge($miembro, $_POST);
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro: <?php echo htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']); ?> | Sistema de Registro</title>
    
    <!-- CSS principal del sistema -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Estilos específicos para el formulario -->
    <style>
        .form-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .form-header h1 {
            margin: 0;
            font-size: 1.8em;
        }
        
        .form-body {
            padding: 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #28a745;
        }
        
        .form-section h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group .required {
            color: #dc3545;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.25);
        }
        
        .form-control.error {
            border-color: #dc3545;
        }
        
        select.form-control {
            cursor: pointer;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }
        
        .error-messages {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .error-messages ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .form-actions {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-success { 
            background: #28a745; 
            color: white; 
        }
        
        .btn-secondary { 
            background: #6c757d; 
            color: white; 
        }
        
        .btn-info { 
            background: #17a2b8; 
            color: white; 
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .breadcrumb {
            margin: 20px 0;
            color: #6c757d;
        }
        
        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .form-hint {
            font-size: 12px;
            color: #6c757d;
            margin-top: 3px;
        }
        
        .input-group {
            display: flex;
            align-items: center;
        }
        
        .input-group-text {
            background: #e9ecef;
            border: 1px solid #ddd;
            border-right: none;
            padding: 10px 12px;
            border-radius: 4px 0 0 4px;
        }
        
        .input-group .form-control {
            border-radius: 0 4px 4px 0;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                margin: 10px;
            }
            
            .form-header {
                padding: 20px;
            }
            
            .form-body {
                padding: 20px;
            }
            
            .btn {
                display: block;
                margin: 5px 0;
                width: 100%;
            }
        }
        
        .char-count {
            font-size: 11px;
            color: #6c757d;
            text-align: right;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- ====================================================================
             BREADCRUMB / NAVEGACIÓN
             ==================================================================== -->
        <div class="breadcrumb">
            <i class="fas fa-home"></i>
            <a href="index.php">Inicio</a> > 
            <a href="list.php">Lista de Miembros</a> > 
            <a href="ver_registro.php?id=<?php echo $id_miembro; ?>">Ver Registro</a> >
            <strong>Editar</strong>
        </div>

        <!-- ====================================================================
             MENSAJES DE ERROR
             ==================================================================== -->
        <?php if (!empty($errores)): ?>
            <div class="error-messages">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Se encontraron los siguientes errores:</strong>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- ====================================================================
             MENSAJES FLASH
             ==================================================================== -->
        <?php 
        if (function_exists('getFlashMessage')) {
            $flashMessage = getFlashMessage();
            if ($flashMessage): 
        ?>
            <div class="mensaje-container <?php echo htmlspecialchars($flashMessage['tipo']); ?>">
                <i class="fas fa-info-circle"></i>
                <?php echo htmlspecialchars($flashMessage['mensaje']); ?>
            </div>
        <?php 
            endif;
        }
        ?>

        <!-- ====================================================================
             FORMULARIO DE EDICIÓN
             ==================================================================== -->
        <div class="form-container">
            
            <!-- ENCABEZADO DEL FORMULARIO -->
            <div class="form-header">
                <h1>
                    <i class="fas fa-user-edit"></i>
                    Editar Registro de Miembro
                </h1>
                <p>
                    Registro #<?php echo $id_miembro; ?> | 
                    CI: <?php echo htmlspecialchars($miembro['ci']); ?>
                </p>
            </div>

            <!-- FORMULARIO -->
            <form method="POST" action="" id="editForm" novalidate>
                <div class="form-body">
                    
                    <div class="form-grid">
                        
                        <!-- ============================================
                             INFORMACIÓN PERSONAL
                             ============================================ -->
                        <div class="form-section">
                            <h3>
                                <i class="fas fa-user"></i>
                                Información Personal
                            </h3>
                            
                            <!-- CI -->
                            <div class="form-group">
                                <label for="ci">
                                    CI <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       id="ci" 
                                       name="ci" 
                                       class="form-control <?php echo in_array('ci', array_column($errores, 'field')) ? 'error' : ''; ?>"
                                       value="<?php echo htmlspecialchars($miembro['ci']); ?>"
                                       required
                                       maxlength="20">
                                <div class="form-hint">Número de cédula de identidad</div>
                            </div>
                            
                            <!-- Nombre -->
                            <div class="form-group">
                                <label for="nombre">
                                    Nombre <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       id="nombre" 
                                       name="nombre" 
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($miembro['nombre']); ?>"
                                       required
                                       maxlength="100">
                            </div>
                            
                            <!-- Apellido -->
                            <div class="form-group">
                                <label for="apellido">
                                    Apellido <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       id="apellido" 
                                       name="apellido" 
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($miembro['apellido']); ?>"
                                       required
                                       maxlength="100">
                            </div>
                            
                            <!-- Fecha de Nacimiento -->
                            <div class="form-group">
                                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                <input type="date" 
                                       id="fecha_nacimiento" 
                                       name="fecha_nacimiento" 
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($miembro['fecha_nacimiento']); ?>"
                                       max="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <!-- Sexo -->
                            <div class="form-group">
                                <label for="sexo">Sexo</label>
                                <select id="sexo" name="sexo" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <option value="M" <?php echo ($miembro['sexo'] === 'M') ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="F" <?php echo ($miembro['sexo'] === 'F') ? 'selected' : ''; ?>>Femenino</option>
                                </select>
                            </div>
                        </div>

                        <!-- ============================================
                             INFORMACIÓN DE CONTACTO
                             ============================================ -->
                        <div class="form-section">
                            <h3>
                                <i class="fas fa-address-book"></i>
                                Información de Contacto
                            </h3>
                            
                            <!-- Teléfono -->
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" 
                                           id="telefono" 
                                           name="telefono" 
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($miembro['telefono']); ?>"
                                           maxlength="20"
                                           placeholder="Ej: 0981234567">
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($miembro['email']); ?>"
                                           maxlength="100"
                                           placeholder="ejemplo@correo.com">
                                </div>
                            </div>
                            
                            <!-- Dirección -->
                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <textarea id="direccion" 
                                          name="direccion" 
                                          class="form-control"
                                          rows="2"
                                          maxlength="200"
                                          placeholder="Dirección completa"><?php echo htmlspecialchars($miembro['direccion']); ?></textarea>
                            </div>
                            
                            <!-- Barrio -->
                            <div class="form-group">
                                <label for="barrio">Barrio</label>
                                <input type="text" 
                                       id="barrio" 
                                       name="barrio" 
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($miembro['barrio']); ?>"
                                       maxlength="100">
                            </div>
                            
                            <!-- Ciudad -->
                            <div class="form-group">
                                <label for="ciudad">Ciudad</label>
                                <input type="text" 
                                       id="ciudad" 
                                       name="ciudad" 
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($miembro['ciudad']); ?>"
                                       maxlength="100">
                            </div>
                        </div>

                        <!-- ============================================
                             INFORMACIÓN EDUCATIVA
                             ============================================ -->
                        <div class="form-section">
                            <h3>
                                <i class="fas fa-graduation-cap"></i>
                                Información Educativa
                            </h3>
                            
                            <!-- Institución Actual -->
                            <div class="form-group">
                                <label for="institucion_actual">Institución Actual</label>
                                <input type="text" 
                                       id="institucion_actual" 
                                       name="institucion_actual" 
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($miembro['institucion_actual']); ?>"
                                       maxlength="150"
                                       placeholder="Nombre de la institución">
                            </div>
                            
                            <!-- Nivel Actual -->
                            <div class="form-group">
                                <label for="nivel_actual">Nivel Actual</label>
                                <select id="nivel_actual" name="nivel_actual" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <option value="Inicial" <?php echo ($miembro['nivel_actual'] === 'Inicial') ? 'selected' : ''; ?>>Inicial</option>
                                    <option value="Primaria" <?php echo ($miembro['nivel_actual'] === 'Primaria') ? 'selected' : ''; ?>>Primaria</option>
                                    <option value="Secundaria" <?php echo ($miembro['nivel_actual'] === 'Secundaria') ? 'selected' : ''; ?>>Secundaria</option>
                                    <option value="Universitario" <?php echo ($miembro['nivel_actual'] === 'Universitario') ? 'selected' : ''; ?>>Universitario</option>
                                    <option value="Técnico" <?php echo ($miembro['nivel_actual'] === 'Técnico') ? 'selected' : ''; ?>>Técnico</option>
                                    <option value="Otro" <?php echo ($miembro['nivel_actual'] === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>
                            
                            <!-- Turno -->
                            <div class="form-group">
                                <label for="turno">Turno</label>
                                <select id="turno" name="turno" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <option value="Mañana" <?php echo ($miembro['turno'] === 'Mañana') ? 'selected' : ''; ?>>Mañana</option>
                                    <option value="Tarde" <?php echo ($miembro['turno'] === 'Tarde') ? 'selected' : ''; ?>>Tarde</option>
                                    <option value="Noche" <?php echo ($miembro['turno'] === 'Noche') ? 'selected' : ''; ?>>Noche</option>
                                </select>
                            </div>
                            
                            <!-- Año de Estudio -->
                            <div class="form-group">
                                <label for="ano_estudio">Año/Curso</label>
                                <input type="text" 
                                       id="ano_estudio" 
                                       name="ano_estudio" 
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($miembro['ano_estudio']); ?>"
                                       maxlength="50"
                                       placeholder="Ej: 1° Año, 5° Grado, etc.">
                            </div>
                        </div>

                        <!-- ============================================
                             INFORMACIÓN ADICIONAL
                             ============================================ -->
                        <div class="form-section" style="grid-column: 1 / -1;">
                            <h3>
                                <i class="fas fa-sticky-note"></i>
                                Información Adicional
                            </h3>
                            
                            <!-- Observaciones -->
                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea id="observaciones" 
                                          name="observaciones" 
                                          class="form-control"
                                          rows="4"
                                          maxlength="500"
                                          placeholder="Notas adicionales, comentarios especiales, etc."><?php 
                                // Verificar si la columna observaciones existe antes de mostrar su valor
                                if (isset($miembro['observaciones'])) {
                                    echo htmlspecialchars($miembro['observaciones']);
                                }
                                ?></textarea>
                                <div class="char-count">
                                    <span id="char-count">0</span>/500 caracteres
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================================================================
                     BOTONES DE ACCIÓN
                     ================================================================ -->
                <div class="form-actions">
                    <!-- Botón Guardar -->
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    
                    <!-- Botón Ver Registro -->
                    <a href="ver_registro.php?id=<?php echo $id_miembro; ?>" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Registro
                    </a>
                    
                    <!-- Botón Cancelar -->
                    <a href="list.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================
         JAVASCRIPT
         ======================================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ===== CONTADOR DE CARACTERES =====
            const observacionesTextarea = document.getElementById('observaciones');
            const charCountSpan = document.getElementById('char-count');
            
            function updateCharCount() {
                const count = observacionesTextarea.value.length;
                charCountSpan.textContent = count;
                
                if (count > 450) {
                    charCountSpan.style.color = '#dc3545';
                } else if (count > 400) {
                    charCountSpan.style.color = '#ffc107';
                } else {
                    charCountSpan.style.color = '#6c757d';
                }
            }
            
            if (observacionesTextarea) {
                updateCharCount(); // Inicializar contador
                observacionesTextarea.addEventListener('input', updateCharCount);
            }
            
            // ===== VALIDACIÓN DEL FORMULARIO =====
            const form = document.getElementById('editForm');
            const requiredFields = form.querySelectorAll('[required]');
            
            // Función para validar un campo
            function validateField(field) {
                const value = field.value.trim();
                const isValid = value !== '';
                
                if (isValid) {
                    field.classList.remove('error');
                    field.classList.add('valid');
                } else {
                    field.classList.add('error');
                    field.classList.remove('valid');
                }
                
                return isValid;
            }
            
            // Validar campos en tiempo real
            requiredFields.forEach(field => {
                field.addEventListener('blur', () => validateField(field));
                field.addEventListener('input', () => {
                    if (field.classList.contains('error')) {
                        validateField(field);
                    }
                });
            });
            
            // Validar formulario antes de enviar
            form.addEventListener('submit', function(e) {
                let isFormValid = true;
                
                // Validar campos requeridos
                requiredFields.forEach(field => {
                    if (!validateField(field)) {
                        isFormValid = false;
                    }
                });
                
                // Validar email si tiene valor
                const emailField = document.getElementById('email');
                if (emailField.value && !isValidEmail(emailField.value)) {
                    emailField.classList.add('error');
                    isFormValid = false;
                    alert('El formato del email no es válido');
                }
                
                // Validar CI (solo números y guiones)
                const ciField = document.getElementById('ci');
                if (!isValidCI(ciField.value)) {
                    ciField.classList.add('error');
                    isFormValid = false;
                    alert('El CI debe contener solo números y guiones');
                }
                
                if (!isFormValid) {
                    e.preventDefault();
                    
                    // Scroll al primer campo con error
                    const firstError = form.querySelector('.error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }
            });
            
            // ===== FUNCIONES DE VALIDACIÓN =====
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            function isValidCI(ci) {
                const ciRegex = /^[0-9\-\.]+$/;
                return ciRegex.test(ci.trim());
            }
            
            // ===== FORMATEO AUTOMÁTICO =====
            
            // Formatear teléfono
            const telefonoField = document.getElementById('telefono');
            if (telefonoField) {
                telefonoField.addEventListener('input', function(e) {
                    // Permitir solo números, espacios, guiones y paréntesis
                    this.value = this.value.replace(/[^0-9\s\-\(\)\+]/g, '');
                });
            }
            
            // Formatear CI
            const ciField = document.getElementById('ci');
            if (ciField) {
                ciField.addEventListener('input', function(e) {
                    // Permitir solo números, puntos y guiones
                    this.value = this.value.replace(/[^0-9\.\-]/g, '');
                });
            }
            
            // ===== CAPITALIZACIÓN AUTOMÁTICA =====
            const nameFields = ['nombre', 'apellido', 'ciudad', 'barrio', 'institucion_actual'];
            nameFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('blur', function() {
                        this.value = capitalizeWords(this.value);
                    });
                }
            });
            
            function capitalizeWords(str) {
                return str.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
            }
            
            // ===== CONFIRMACIÓN DE CAMBIOS =====
            let formChanged = false;
            const formElements = form.querySelectorAll('input, select, textarea');
            
            // Guardar valores originales
            const originalValues = {};
            formElements.forEach(element => {
                originalValues[element.name] = element.value;
            });
            
            // Detectar cambios
            formElements.forEach(element => {
                element.addEventListener('change', function() {
                    if (this.value !== originalValues[this.name]) {
                        formChanged = true;
                    }
                });
            });
            
            // Advertir si hay cambios sin guardar
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = '¿Está seguro de que desea salir? Los cambios no guardados se perderán.';
                    return e.returnValue;
                }
            });
            
            // No mostrar advertencia al enviar el formulario
            form.addEventListener('submit', function() {
                formChanged = false;
            });
            
            // ===== ATAJOS DE TECLADO =====
            document.addEventListener('keydown', function(e) {
                // Ctrl + S para guardar
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    form.submit();
                }
                
                // Escape para cancelar
                if (e.key === 'Escape') {
                    if (confirm('¿Está seguro de que desea cancelar? Los cambios no guardados se perderán.')) {
                        window.location.href = 'list.php';
                    }
                }
            });
            
            // ===== EFECTOS VISUALES =====
            const formSections = document.querySelectorAll('.form-section');
            formSections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    section.style.transition = 'all 0.5s ease';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // ===== AUTO-FOCUS EN PRIMER CAMPO CON ERROR =====
            const errorField = document.querySelector('.form-control.error');
            if (errorField) {
                errorField.focus();
                errorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                // Si no hay errores, enfocar el primer campo
                const firstField = document.getElementById('ci');
                if (firstField) {
                    firstField.focus();
                }
            }
            
            // ===== INDICADOR DE CARGA AL ENVIAR =====
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
                    submitBtn.disabled = true;
                    
                    // Deshabilitar todos los campos para evitar cambios durante el envío
                    formElements.forEach(element => {
                        element.disabled = true;
                    });
                }
            });
            
            // ===== TOOLTIPS INFORMATIVOS =====
            const tooltips = {
                'ci': 'Ingrese el número de cédula sin espacios',
                'email': 'Formato: ejemplo@correo.com',
                'fecha_nacimiento': 'La fecha no puede ser futura',
                'telefono': 'Incluya código de área si es necesario'
            };
            
            Object.keys(tooltips).forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.title = tooltips[fieldId];
                }
            });
            
            console.log('✅ Formulario de edición cargado correctamente');
        });
        
        // ===== FUNCIÓN PARA LIMPIAR FORMULARIO =====
        function resetForm() {
            if (confirm('¿Está seguro de que desea restablecer todos los campos?')) {
                document.getElementById('editForm').reset();
                
                // Limpiar clases de validación
                const fields = document.querySelectorAll('.form-control');
                fields.forEach(field => {
                    field.classList.remove('error', 'valid');
                });
                
                // Actualizar contador de caracteres
                const charCountSpan = document.getElementById('char-count');
                if (charCountSpan) {
                    charCountSpan.textContent = '0';
                    charCountSpan.style.color = '#6c757d';
                }
            }
        }
        
        // ===== FUNCIÓN PARA VALIDAR DUPLICADOS DE CI =====
        function checkCIDuplicate() {
            const ciField = document.getElementById('ci');
            const originalCI = '<?php echo htmlspecialchars($miembro['ci']); ?>';
            
            if (ciField.value !== originalCI && ciField.value.trim() !== '') {
                // Aquí podrías hacer una llamada AJAX para verificar duplicados
                // Por ahora solo mostramos una advertencia visual
                console.log('Verificando CI:', ciField.value);
            }
        }
    </script>
</body>
</html>