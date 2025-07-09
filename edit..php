<?php
// Iniciar sesión
session_start();

// Incluir archivo de configuración de la base de datos
require_once 'config/database.php';

// Verificación de token
if (!isset($_GET['token']) || $_GET['token'] !== 'demo123') {
    header("Location: login.php");
    exit();
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?token=demo123");
    exit();
}

$id = $_GET['id'];
$mensaje = '';
$tipo_mensaje = '';

// Crear instancia de la base de datos y obtener conexión
$database = new Database();
$db = $database->getConnection();

// Función para limpiar datos de entrada
function limpiarDato($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

// Procesar formulario si se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // DATOS PERSONALES
        $ci = limpiarDato($_POST["ci"]);
        $nombre = limpiarDato($_POST["nombre"]);
        $apellido = limpiarDato($_POST["apellido"]);
        $fecha_nacimiento = limpiarDato($_POST["fecha_nacimiento"]);
        $lugar = limpiarDato($_POST["lugar"]);
        $profesion = limpiarDato($_POST["profesion"]);
        $direccion = limpiarDato($_POST["direccion"]);
        $telefono = limpiarDato($_POST["telefono"]);
        $ciudad = limpiarDato($_POST["ciudad"]);
        $barrio = limpiarDato($_POST["barrio"]);
        $esposa = isset($_POST["esposa"]) ? limpiarDato($_POST["esposa"]) : "";
        $hijos = isset($_POST["hijos"]) ? limpiarDato($_POST["hijos"]) : "";
        $madre = isset($_POST["madre"]) ? limpiarDato($_POST["madre"]) : "";
        $padre = isset($_POST["padre"]) ? limpiarDato($_POST["padre"]) : "";
        
        // DATOS LABORALES
        $direccion_laboral = limpiarDato($_POST["direccion_laboral"]);
        $empresa = isset($_POST["empresa"]) ? limpiarDato($_POST["empresa"]) : "";
        
        // DATOS LOGIALES
        $institucion_actual = limpiarDato($_POST["logia_actual"]);
        $nivel_actual = limpiarDato($_POST["grado_masonico"]);
        $nivel_superior = isset($_POST["grado_capitular"]) ? limpiarDato($_POST["grado_capitular"]) : "";
        $fecha_ingreso = limpiarDato($_POST["fecha_iniciacion"]);
        $institucion_ingreso = limpiarDato($_POST["logia_iniciacion"]);
        
        // DATOS MÉDICOS
        $grupo_sanguineo = limpiarDato($_POST["grupo_sanguineo"]);
        $enfermedades_base = isset($_POST["enfermedades_base"]) ? limpiarDato($_POST["enfermedades_base"]) : "";
        $seguro_privado = isset($_POST["seguro_privado"]) ? limpiarDato($_POST["seguro_privado"]) : "No";
        $ips = isset($_POST["ips"]) ? limpiarDato($_POST["ips"]) : "No";
        $alergias = isset($_POST["alergias"]) ? limpiarDato($_POST["alergias"]) : "";
        $numero_emergencia = limpiarDato($_POST["numero_emergencia"]);
        $contacto_emergencia = limpiarDato($_POST["contacto_emergencia"]);
        
        // Verificar si el CI ya existe en otro registro
        $check_sql = "SELECT id FROM miembros WHERE ci = :ci AND id != :id";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':ci', $ci);
        $check_stmt->bindParam(':id', $id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            throw new Exception("Ya existe otro registro con este número de CI.");
        }
        
        // Manejo de documentos - conservar los existentes y agregar nuevos si los hay
        $documentosActuales = "";
        
        // Obtener documentos actuales
        $sql_docs = "SELECT documentos FROM miembros WHERE id = :id";
        $stmt_docs = $db->prepare($sql_docs);
        $stmt_docs->bindParam(':id', $id);
        $stmt_docs->execute();
        $row_docs = $stmt_docs->fetch(PDO::FETCH_ASSOC);
        $documentosActuales = $row_docs['documentos'];
        
        // Procesar nuevos documentos si existen
        $nuevosDocs = [];
        if (isset($_FILES["certificados"]) && is_array($_FILES["certificados"]["name"])) {
            $directorio_destino = "uploads/documentos/";
            
            // Crear el directorio si no existe
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }
            
            // Procesar cada archivo nuevo
            for ($i = 0; $i < count($_FILES["certificados"]["name"]); $i++) {
                if ($_FILES["certificados"]["error"][$i] == 0) {
                    $nombre_archivo = uniqid() . "_" . basename($_FILES["certificados"]["name"][$i]);
                    $ruta_archivo = $directorio_destino . $nombre_archivo;
                    
                    if (move_uploaded_file($_FILES["certificados"]["tmp_name"][$i], $ruta_archivo)) {
                        $nuevosDocs[] = $ruta_archivo;
                    }
                }
            }
        }
        
        // Combinar documentos nuevos con existentes
        $todosDocs = [];
        if (!empty($documentosActuales)) {
            $todosDocs = explode(",", $documentosActuales);
        }
        $todosDocs = array_merge($todosDocs, $nuevosDocs);
        $documentosFinal = implode(",", $todosDocs);
        
        // Actualizar datos en la base de datos
        $sql = "UPDATE miembros SET 
                ci = :ci, 
                nombre = :nombre, 
                apellido = :apellido, 
                fecha_nacimiento = :fecha_nacimiento, 
                lugar = :lugar, 
                profesion = :profesion, 
                direccion = :direccion, 
                telefono = :telefono, 
                ciudad = :ciudad, 
                barrio = :barrio, 
                esposa = :esposa, 
                hijos = :hijos, 
                madre = :madre, 
                padre = :padre,
                direccion_laboral = :direccion_laboral, 
                empresa = :empresa,
                institucion_actual = :institucion_actual, 
                nivel_actual = :nivel_actual, 
                nivel_superior = :nivel_superior, 
                fecha_ingreso = :fecha_ingreso, 
                institucion_ingreso = :institucion_ingreso, 
                documentos = :documentos,
                grupo_sanguineo = :grupo_sanguineo, 
                enfermedades_base = :enfermedades_base, 
                seguro_privado = :seguro_privado, 
                ips = :ips, 
                alergias = :alergias, 
                numero_emergencia = :numero_emergencia, 
                contacto_emergencia = :contacto_emergencia
                WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        
        // Vincular parámetros
        $stmt->bindParam(':ci', $ci);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':lugar', $lugar);
        $stmt->bindParam(':profesion', $profesion);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':barrio', $barrio);
        $stmt->bindParam(':esposa', $esposa);
        $stmt->bindParam(':hijos', $hijos);
        $stmt->bindParam(':madre', $madre);
        $stmt->bindParam(':padre', $padre);
        $stmt->bindParam(':direccion_laboral', $direccion_laboral);
        $stmt->bindParam(':empresa', $empresa);
        $stmt->bindParam(':institucion_actual', $institucion_actual);
        $stmt->bindParam(':nivel_actual', $nivel_actual);
        $stmt->bindParam(':nivel_superior', $nivel_superior);
        $stmt->bindParam(':fecha_ingreso', $fecha_ingreso);
        $stmt->bindParam(':institucion_ingreso', $institucion_ingreso);
        $stmt->bindParam(':documentos', $documentosFinal);
        $stmt->bindParam(':grupo_sanguineo', $grupo_sanguineo);
        $stmt->bindParam(':enfermedades_base', $enfermedades_base);
        $stmt->bindParam(':seguro_privado', $seguro_privado);
        $stmt->bindParam(':ips', $ips);
        $stmt->bindParam(':alergias', $alergias);
        $stmt->bindParam(':numero_emergencia', $numero_emergencia);
        $stmt->bindParam(':contacto_emergencia', $contacto_emergencia);
        $stmt->bindParam(':id', $id);
        
        // Ejecutar la actualización
        if ($stmt->execute()) {
            $mensaje = "Registro actualizado exitosamente";
            $tipo_mensaje = "success";
        } else {
            throw new Exception("Error al actualizar el registro.");
        }
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = "error";
    }
}

// Obtener datos del miembro para mostrar en el formulario
$sql = "SELECT * FROM miembros WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

// Verificar si existe el registro
if ($stmt->rowCount() == 0) {
    header("Location: list.php?token=demo123");
    exit();
}

// Obtener los datos del miembro
$miembro = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Editar Registro</h1>
            <p>Actualice la información del miembro</p>
        </div>
        
        <?php if (!empty($mensaje)): ?>
        <div class="mensaje-container <?php echo $tipo_mensaje; ?>" style="margin: 20px auto; padding: 15px; border-radius: 5px; text-align: center; width: 80%; 
            <?php echo ($tipo_mensaje == 'success') ? 'background-color: #dcfce7; color: #047857; border: 1px solid #10b981;' : 'background-color: #fee2e2; color: #b91c1c; border: 1px solid #ef4444;'; ?>">
            <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form action="edit.php?id=<?php echo $id; ?>&token=<?php echo htmlspecialchars($_GET['token']); ?>" method="POST" enctype="multipart/form-data" id="edit-form">
                <!-- DATOS PERSONALES -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-user"></i> DATOS PERSONALES
                    </h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ci">CI:<span class="required">*</span></label>
                            <input type="text" id="ci" name="ci" value="<?php echo htmlspecialchars($miembro['ci']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nombre">Nombre:<span class="required">*</span></label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($miembro['nombre']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="apellido">Apellido:<span class="required">*</span></label>
                            <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($miembro['apellido']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento:<span class="required">*</span></label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($miembro['fecha_nacimiento']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lugar">Lugar:<span class="required">*</span></label>
                            <input type="text" id="lugar" name="lugar" value="<?php echo htmlspecialchars($miembro['lugar']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="profesion">Profesión:<span class="required">*</span></label>
                            <input type="text" id="profesion" name="profesion" value="<?php echo htmlspecialchars($miembro['profesion']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="direccion">Dirección:<span class="required">*</span></label>
                            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($miembro['direccion']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono">Teléfono:<span class="required">*</span></label>
                            <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($miembro['telefono']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ciudad">Ciudad:<span class="required">*</span></label>
                            <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($miembro['ciudad']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="barrio">Barrio:<span class="required">*</span></label>
                            <input type="text" id="barrio" name="barrio" value="<?php echo htmlspecialchars($miembro['barrio']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="esposa">Esposa:</label>
                            <input type="text" id="esposa" name="esposa" value="<?php echo htmlspecialchars($miembro['esposa']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hijos">Hijos:</label>
                            <textarea id="hijos" name="hijos" rows="2"><?php echo htmlspecialchars($miembro['hijos']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="madre">Madre:</label>
                            <input type="text" id="madre" name="madre" value="<?php echo htmlspecialchars($miembro['madre']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="padre">Padre:</label>
                            <input type="text" id="padre" name="padre" value="<?php echo htmlspecialchars($miembro['padre']); ?>">
                        </div>
                    </div>
                </div>
                
                <!-- DATOS LABORALES -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-briefcase"></i> DATOS LABORALES
                    </h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="direccion_laboral">Dirección Laboral:<span class="required">*</span></label>
                            <input type="text" id="direccion_laboral" name="direccion_laboral" value="<?php echo htmlspecialchars($miembro['direccion_laboral']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="empresa">Empresa:</label>
                            <input type="text" id="empresa" name="empresa" value="<?php echo htmlspecialchars($miembro['empresa']); ?>">
                        </div>
                    </div>
                </div>
                
                <!-- DATOS LOGIALES -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-monument"></i> DATOS LOGIALES
                    </h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="logia_actual">Logia Actual:<span class="required">*</span></label>
                            <input type="text" id="logia_actual" name="logia_actual" value="<?php echo htmlspecialchars($miembro['institucion_actual']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="grado_masonico">Grado Masónico:<span class="required">*</span></label>
                            <select id="grado_masonico" name="grado_masonico" required>
                                <option value="">Seleccione Grado</option>
                                <option value="aprendiz" <?php echo ($miembro['nivel_actual'] == 'aprendiz') ? 'selected' : ''; ?>>Aprendiz</option>
                                <option value="companero" <?php echo ($miembro['nivel_actual'] == 'companero') ? 'selected' : ''; ?>>Compañero</option>
                                <option value="maestro" <?php echo ($miembro['nivel_actual'] == 'maestro') ? 'selected' : ''; ?>>Maestro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="grado_capitular">Grado Capitular:</label>
                            <select id="grado_capitular" name="grado_capitular">
                                <option value="">Seleccione Grado</option>
                                <option value="primer_grado" <?php echo ($miembro['nivel_superior'] == 'primer_grado') ? 'selected' : ''; ?>>Primer Grado</option>
                                <option value="segundo_grado" <?php echo ($miembro['nivel_superior'] == 'segundo_grado') ? 'selected' : ''; ?>>Segundo Grado</option>
                                <option value="tercer_grado" <?php echo ($miembro['nivel_superior'] == 'tercer_grado') ? 'selected' : ''; ?>>Tercer Grado</option>
                                <option value="no_aplica" <?php echo ($miembro['nivel_superior'] == 'no_aplica') ? 'selected' : ''; ?>>No Aplica</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_iniciacion">Fecha de Iniciación:<span class="required">*</span></label>
                            <input type="date" id="fecha_iniciacion" name="fecha_iniciacion" value="<?php echo htmlspecialchars($miembro['fecha_ingreso']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="logia_iniciacion">Logia de Iniciación:<span class="required">*</span></label>
                            <input type="text" id="logia_iniciacion" name="logia_iniciacion" value="<?php echo htmlspecialchars($miembro['institucion_ingreso']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="certificados">Certificados adicionales:</label>
                            <div class="file-upload-container">
                                <input type="file" id="certificados" name="certificados[]" multiple accept="image/jpeg,image/png,application/pdf">
                                <div class="file-preview"></div>
                            </div>
                            
                            <?php if (!empty($miembro['documentos'])): ?>
                            <div class="current-documents">
                                <p>Documentos actuales:</p>
                                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 5px;">
                                    <?php 
                                    $documentos = explode(",", $miembro['documentos']);
                                    foreach ($documentos as $doc):
                                        $nombre = basename($doc);
                                        $ext = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
                                        $icono = ($ext == 'pdf') ? 'fa-file-pdf' : (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']) ? 'fa-file-image' : 'fa-file');
                                    ?>
                                    <div style="text-align: center; width: 100px;">
                                        <i class="fas <?php echo $icono; ?>" style="font-size: 24px; color: var(--primary-color);"></i>
                                        <div style="font-size: 12px; margin-top: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?php echo htmlspecialchars($nombre); ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- DATOS MÉDICOS -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-heartbeat"></i> DATOS MÉDICOS
                    </h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="grupo_sanguineo">Grupo Sanguíneo:<span class="required">*</span></label>
                            <select id="grupo_sanguineo" name="grupo_sanguineo" required>
                                <option value="">Seleccione...</option>
                                <option value="A+" <?php echo ($miembro['grupo_sanguineo'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                                <option value="A-" <?php echo ($miembro['grupo_sanguineo'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                                <option value="B+" <?php echo ($miembro['grupo_sanguineo'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                                <option value="B-" <?php echo ($miembro['grupo_sanguineo'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                                <option value="AB+" <?php echo ($miembro['grupo_sanguineo'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                <option value="AB-" <?php echo ($miembro['grupo_sanguineo'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                                <option value="O+" <?php echo ($miembro['grupo_sanguineo'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                                <option value="O-" <?php echo ($miembro['grupo_sanguineo'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="enfermedades_base">Enfermedades Base:</label>
                            <textarea id="enfermedades_base" name="enfermedades_base" rows="2"><?php echo htmlspecialchars($miembro['enfermedades_base']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <fieldset class="radio-fieldset">
                                <legend>¿Cuenta con Seguro Privado?</legend>
                                <div class="radio-options">
                                    <div class="radio-group">
                                        <input type="radio" id="seguro_si" name="seguro_privado" value="Si" <?php echo ($miembro['seguro_privado'] == 'Si') ? 'checked' : ''; ?>>
                                        <label for="seguro_si">Sí</label>
                                    </div>
                                    <div class="radio-group">
                                        <input type="radio" id="seguro_no" name="seguro_privado" value="No" <?php echo ($miembro['seguro_privado'] == 'No') ? 'checked' : ''; ?>>
                                        <label for="seguro_no">No</label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        
                        <div class="form-group">
                            <fieldset class="radio-fieldset">
                                <legend>¿IPS?</legend>
                                <div class="radio-options">
                                    <div class="radio-group">
                                        <input type="radio" id="ips_si" name="ips" value="Si" <?php echo ($miembro['ips'] == 'Si') ? 'checked' : ''; ?>>
                                        <label for="ips_si">Sí</label>
                                    </div>
                                    <div class="radio-group">
                                        <input type="radio" id="ips_no" name="ips" value="No" <?php echo ($miembro['ips'] == 'No') ? 'checked' : ''; ?>>
                                        <label for="ips_no">No</label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="alergias">Alergias:</label>
                            <textarea id="alergias" name="alergias" rows="2"><?php echo htmlspecialchars($miembro['alergias']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="numero_emergencia">Número en caso de Emergencias:<span class="required">*</span></label>
                            <input type="tel" id="numero_emergencia" name="numero_emergencia" value="<?php echo htmlspecialchars($miembro['numero_emergencia']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="contacto_emergencia">Contacto en Caso de Emergencias:<span class="required">*</span></label>
                            <input type="text" id="contacto_emergencia" name="contacto_emergencia" value="<?php echo htmlspecialchars($miembro['contacto_emergencia']); ?>" required>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="btn-container">
                    <a href="list.php?token=<?php echo htmlspecialchars($_GET['token']); ?>" class="btn">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
                
                <!-- Indicador de campos requeridos -->
                <div class="required-fields-note">
                    <span class="required">*</span> Campos requeridos
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/js/validation.js" defer></script>
</body>
</html>