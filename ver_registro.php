<?php
/**
 * VER REGISTRO DE MIEMBRO - Sistema de Registro
 * Archivo: ver_registro.php
 * Descripción: Página para visualizar los detalles completos de un miembro registrado
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
    // Si no hay ID válido, redirigir a la lista con mensaje de error
    if (function_exists('setFlashMessage')) {
        setFlashMessage('error', 'ID de registro no válido');
    }
    header("Location: list.php");
    exit();
}

$id_miembro = (int)$_GET['id'];

// ============================================================================
// 3. CONEXIÓN A LA BASE DE DATOS Y CONSULTA
// ============================================================================

try {
    // Crear instancia de la clase Database
    $database = new Database();
    $db = $database->getConnection();
    
    // Consulta para obtener todos los datos del miembro
    $sql = "SELECT * FROM miembros WHERE id = :id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id_miembro, PDO::PARAM_INT);
    $stmt->execute();
    
    // Verificar si se encontró el registro
    if ($stmt->rowCount() === 0) {
        // Si no se encuentra el registro, redirigir con mensaje de error
        if (function_exists('setFlashMessage')) {
            setFlashMessage('error', 'El registro solicitado no existe');
        }
        header("Location: list.php");
        exit();
    }
    
    // Obtener los datos del miembro
    $miembro = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Manejo de errores de base de datos
    $error_message = "Error al consultar la base de datos: " . $e->getMessage();
    if (function_exists('setFlashMessage')) {
        setFlashMessage('error', 'Error al cargar el registro');
    }
    header("Location: list.php");
    exit();
}

// ============================================================================
// 4. FUNCIONES AUXILIARES
// ============================================================================

/**
 * Función para formatear fechas
 */
function formatearFecha($fecha) {
    if (empty($fecha) || $fecha === '0000-00-00') {
        return 'No especificado';
    }
    return date('d/m/Y', strtotime($fecha));
}

/**
 * Función para formatear fecha y hora
 */
function formatearFechaHora($fecha) {
    if (empty($fecha)) {
        return 'No especificado';
    }
    return date('d/m/Y H:i:s', strtotime($fecha));
}

/**
 * Función para calcular la edad
 */
function calcularEdad($fecha_nacimiento) {
    if (empty($fecha_nacimiento) || $fecha_nacimiento === '0000-00-00') {
        return 'No especificado';
    }
    
    $fecha_nac = new DateTime($fecha_nacimiento);
    $fecha_actual = new DateTime();
    $edad = $fecha_actual->diff($fecha_nac);
    
    return $edad->y . ' años';
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Registro: <?php echo htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']); ?> | Sistema de Registro</title>
    
    <!-- CSS principal del sistema -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Estilos específicos para esta página -->
    <style>
        .registro-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .registro-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .registro-header h1 {
            margin: 0;
            font-size: 2em;
        }
        
        .registro-header .subtitle {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        .registro-body {
            padding: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #007bff;
        }
        
        .info-section h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-item {
            margin-bottom: 12px;
            display: flex;
            flex-wrap: wrap;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            min-width: 120px;
            margin-right: 10px;
        }
        
        .info-value {
            color: #333;
            flex: 1;
        }
        
        .info-value.empty {
            color: #999;
            font-style: italic;
        }
        
        .actions-container {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary { background: #007bff; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
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
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .info-item {
                flex-direction: column;
            }
            
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
            
            .registro-container {
                margin: 10px;
            }
            
            .registro-header {
                padding: 20px;
            }
            
            .registro-body {
                padding: 20px;
            }
        }
        
        .print-hidden {
            display: block;
        }
        
        @media print {
            .print-hidden {
                display: none !important;
            }
            
            .registro-container {
                box-shadow: none;
                border: 1px solid #ccc;
            }
            
            .btn:hover {
                transform: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- ====================================================================
             BREADCRUMB / NAVEGACIÓN
             ==================================================================== -->
        <div class="breadcrumb print-hidden">
            <i class="fas fa-home"></i>
            <a href="index.php">Inicio</a> > 
            <a href="list.php">Lista de Miembros</a> > 
            <strong>Ver Registro</strong>
        </div>

        <!-- ====================================================================
             MENSAJES FLASH
             ==================================================================== -->
        <?php 
        if (function_exists('getFlashMessage')) {
            $flashMessage = getFlashMessage();
            if ($flashMessage): 
        ?>
            <div class="mensaje-container <?php echo htmlspecialchars($flashMessage['tipo']); ?> print-hidden">
                <i class="fas fa-info-circle"></i>
                <?php echo htmlspecialchars($flashMessage['mensaje']); ?>
            </div>
        <?php 
            endif;
        }
        ?>

        <!-- ====================================================================
             CONTENEDOR PRINCIPAL DEL REGISTRO
             ==================================================================== -->
        <div class="registro-container">
            
            <!-- ENCABEZADO DEL REGISTRO -->
            <div class="registro-header">
                <h1>
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']); ?>
                </h1>
                <p class="subtitle">
                    CI: <?php echo htmlspecialchars($miembro['ci']); ?> | 
                    Registro #<?php echo $miembro['id']; ?>
                    <span class="status-badge status-active">Activo</span>
                </p>
            </div>

            <!-- CUERPO CON LA INFORMACIÓN -->
            <div class="registro-body">
                
                <div class="info-grid">
                    
                    <!-- ============================================
                         INFORMACIÓN PERSONAL
                         ============================================ -->
                    <div class="info-section">
                        <h3>
                            <i class="fas fa-user"></i>
                            Información Personal
                        </h3>
                        
                        <div class="info-item">
                            <span class="info-label">CI:</span>
                            <span class="info-value"><?php echo htmlspecialchars($miembro['ci']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Nombre:</span>
                            <span class="info-value"><?php echo htmlspecialchars($miembro['nombre']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Apellido:</span>
                            <span class="info-value"><?php echo htmlspecialchars($miembro['apellido']); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Fecha Nacimiento:</span>
                            <span class="info-value <?php echo empty($miembro['fecha_nacimiento']) ? 'empty' : ''; ?>">
                                <?php echo formatearFecha($miembro['fecha_nacimiento']); ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Edad:</span>
                            <span class="info-value">
                                <?php echo calcularEdad($miembro['fecha_nacimiento']); ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Sexo:</span>
                            <span class="info-value <?php echo empty($miembro['sexo']) ? 'empty' : ''; ?>">
                                <?php 
                                if (!empty($miembro['sexo'])) {
                                    echo $miembro['sexo'] === 'M' ? 'Masculino' : 'Femenino';
                                } else {
                                    echo 'No especificado';
                                }
                                ?>
                            </span>
                        </div>
                    </div>

                    <!-- ============================================
                         INFORMACIÓN DE CONTACTO
                         ============================================ -->
                    <div class="info-section">
                        <h3>
                            <i class="fas fa-address-book"></i>
                            Información de Contacto
                        </h3>
                        
                        <div class="info-item">
                            <span class="info-label">Teléfono:</span>
                            <span class="info-value <?php echo empty($miembro['telefono']) ? 'empty' : ''; ?>">
                                <?php if (!empty($miembro['telefono'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($miembro['telefono']); ?>">
                                        <i class="fas fa-phone"></i>
                                        <?php echo htmlspecialchars($miembro['telefono']); ?>
                                    </a>
                                <?php else: ?>
                                    No especificado
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value <?php echo empty($miembro['email']) ? 'empty' : ''; ?>">
                                <?php if (!empty($miembro['email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($miembro['email']); ?>">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo htmlspecialchars($miembro['email']); ?>
                                    </a>
                                <?php else: ?>
                                    No especificado
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Dirección:</span>
                            <span class="info-value <?php echo empty($miembro['direccion']) ? 'empty' : ''; ?>">
                                <?php 
                                if (!empty($miembro['direccion'])) {
                                    echo htmlspecialchars($miembro['direccion']);
                                } else {
                                    echo 'No especificado';
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Barrio:</span>
                            <span class="info-value <?php echo empty($miembro['barrio']) ? 'empty' : ''; ?>">
                                <?php echo htmlspecialchars($miembro['barrio'] ?: 'No especificado'); ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Ciudad:</span>
                            <span class="info-value <?php echo empty($miembro['ciudad']) ? 'empty' : ''; ?>">
                                <?php echo htmlspecialchars($miembro['ciudad'] ?: 'No especificado'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- ============================================
                         INFORMACIÓN EDUCATIVA
                         ============================================ -->
                    <div class="info-section">
                        <h3>
                            <i class="fas fa-graduation-cap"></i>
                            Información Educativa
                        </h3>
                        
                        <div class="info-item">
                            <span class="info-label">Institución Actual:</span>
                            <span class="info-value <?php echo empty($miembro['institucion_actual']) ? 'empty' : ''; ?>">
                                <?php echo htmlspecialchars($miembro['institucion_actual'] ?: 'No especificado'); ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Nivel Actual:</span>
                            <span class="info-value <?php echo empty($miembro['nivel_actual']) ? 'empty' : ''; ?>">
                                <?php echo htmlspecialchars($miembro['nivel_actual'] ?: 'No especificado'); ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Turno:</span>
                            <span class="info-value <?php echo empty($miembro['turno']) ? 'empty' : ''; ?>">
                                <?php echo htmlspecialchars($miembro['turno'] ?: 'No especificado'); ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Año de Estudio:</span>
                            <span class="info-value <?php echo empty($miembro['ano_estudio']) ? 'empty' : ''; ?>">
                                <?php echo htmlspecialchars($miembro['ano_estudio'] ?: 'No especificado'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- ============================================
                         INFORMACIÓN DEL SISTEMA
                         ============================================ -->
                    <div class="info-section">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Información del Sistema
                        </h3>
                        
                        <div class="info-item">
                            <span class="info-label">ID del Registro:</span>
                            <span class="info-value"><?php echo $miembro['id']; ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Fecha de Registro:</span>
                            <span class="info-value">
                                <i class="fas fa-calendar-plus"></i>
                                <?php echo formatearFechaHora($miembro['fecha_registro']); ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($miembro['fecha_actualizacion'])): ?>
                        <div class="info-item">
                            <span class="info-label">Última Actualización:</span>
                            <span class="info-value">
                                <i class="fas fa-calendar-edit"></i>
                                <?php echo formatearFechaHora($miembro['fecha_actualizacion']); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="info-item">
                            <span class="info-label">Estado:</span>
                            <span class="info-value">
                                <span class="status-badge status-active">
                                    <i class="fas fa-check-circle"></i>
                                    Activo
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- ============================================
                         INFORMACIÓN ADICIONAL (si existe)
                         ============================================ -->
                    <?php if (!empty($miembro['observaciones']) || !empty($miembro['notas'])): ?>
                    <div class="info-section" style="grid-column: 1 / -1;">
                        <h3>
                            <i class="fas fa-sticky-note"></i>
                            Información Adicional
                        </h3>
                        
                        <?php if (!empty($miembro['observaciones'])): ?>
                        <div class="info-item">
                            <span class="info-label">Observaciones:</span>
                            <span class="info-value">
                                <?php echo nl2br(htmlspecialchars($miembro['observaciones'])); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($miembro['notas'])): ?>
                        <div class="info-item">
                            <span class="info-label">Notas:</span>
                            <span class="info-value">
                                <?php echo nl2br(htmlspecialchars($miembro['notas'])); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ================================================================
                 BOTONES DE ACCIÓN
                 ================================================================ -->
            <div class="actions-container print-hidden">
                <!-- Botón Editar -->
                <a href="editar_registro.php?id=<?php echo $miembro['id']; ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar Registro
                </a>
                
                <!-- Botón Imprimir -->
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                
                <!-- Botón Volver a la Lista -->
                <a href="list.php" class="btn btn-primary">
                    <i class="fas fa-list"></i> Volver a la Lista
                </a>
                
                <!-- Botón Eliminar -->
                <a href="eliminar_registro.php?id=<?php echo $miembro['id']; ?>" 
                   class="btn btn-danger delete-btn"
                   data-nombre="<?php echo htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']); ?>">
                    <i class="fas fa-trash"></i> Eliminar Registro
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================================================
         JAVASCRIPT
         ======================================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ===== CONFIRMACIÓN DE ELIMINACIÓN =====
            const deleteBtn = document.querySelector('.delete-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const nombreMiembro = this.getAttribute('data-nombre');
                    
                    const confirmacion = confirm(
                        `¿Está ABSOLUTAMENTE SEGURO de que desea eliminar el registro de "${nombreMiembro}"?\n\n` +
                        `Esta acción eliminará PERMANENTEMENTE todos los datos y NO se puede deshacer.\n\n` +
                        `Haga clic en "Aceptar" solo si está completamente seguro.`
                    );
                    
                    if (confirmacion) {
                        // Segunda confirmación para estar extra seguro
                        const confirmacion2 = confirm(
                            `ÚLTIMA CONFIRMACIÓN:\n\n` +
                            `Se eliminará permanentemente el registro de "${nombreMiembro}".\n\n` +
                            `¿Continuar con la eliminación?`
                        );
                        
                        if (confirmacion2) {
                            window.location.href = this.href;
                        }
                    }
                });
            }
            
            // ===== ATAJOS DE TECLADO =====
            document.addEventListener('keydown', function(e) {
                // Ctrl + P para imprimir
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
                
                // Escape para volver a la lista
                if (e.key === 'Escape') {
                    window.location.href = 'list.php';
                }
                
                // Ctrl + E para editar
                if (e.ctrlKey && e.key === 'e') {
                    e.preventDefault();
                    window.location.href = 'editar_registro.php?id=<?php echo $miembro['id']; ?>';
                }
            });
            
            // ===== EFECTOS VISUALES =====
            const infoSections = document.querySelectorAll('.info-section');
            infoSections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    section.style.transition = 'all 0.5s ease';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            console.log('✅ Vista de registro cargada correctamente');
        });
        
        // ===== FUNCIÓN PARA COMPARTIR (placeholder) =====
        function compartirRegistro() {
            if (navigator.share) {
                navigator.share({
                    title: 'Registro de <?php echo htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']); ?>',
                    text: 'Información del miembro registrado en el sistema',
                    url: window.location.href
                });
            } else {
                // Fallback: copiar URL al portapapeles
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('URL copiada al portapapeles');
                });
            }
        }
    </script>
</body>
</html>