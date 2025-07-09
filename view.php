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

// Crear instancia de la base de datos y obtener conexión
$database = new Database();
$db = $database->getConnection();

// Preparar consulta para obtener los datos del miembro
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

// Función helper para mostrar campos logiales correctamente
function mostrarCampoLogial($valor, $campoOriginal) {
    if ($campoOriginal == 'nivel_actual') {
        switch ($valor) {
            case 'aprendiz': return 'Aprendiz';
            case 'companero': return 'Compañero';
            case 'maestro': return 'Maestro';
            default: return htmlspecialchars($valor);
        }
    } elseif ($campoOriginal == 'nivel_superior') {
        switch ($valor) {
            case 'primer_grado': return 'Primer Grado';
            case 'segundo_grado': return 'Segundo Grado';
            case 'tercer_grado': return 'Tercer Grado';
            case 'no_aplica': return 'No Aplica';
            default: return htmlspecialchars($valor ?: 'No especificado');
        }
    }
    return htmlspecialchars($valor);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Registro</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .detail-section {
            margin-bottom: 30px;
        }
        
        .detail-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .detail-row {
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
        }
        
        .detail-item {
            flex: 1 0 300px;
            margin-right: 20px;
            margin-bottom: 10px;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--primary-light);
            display: block;
            margin-bottom: 3px;
        }
        
        .detail-value {
            color: var(--text-color);
        }
        
        .document-preview {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .document-preview img {
            max-width: 150px;
            max-height: 150px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            object-fit: cover;
        }
        
        .document-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .document-icon {
            font-size: 2rem;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .document-name {
            font-size: 0.8rem;
            text-align: center;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Detalles del Registro</h1>
            <p>Información completa del miembro</p>
        </div>
        
        <div class="form-container">
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-user"></i> DATOS PERSONALES
                </h2>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">CI:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['ci']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Nombre:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Fecha de Nacimiento:</span>
                        <span class="detail-value"><?php echo date('d/m/Y', strtotime($miembro['fecha_nacimiento'])); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Lugar:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['lugar']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Profesión:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['profesion']); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Dirección:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['direccion']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Teléfono:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['telefono']); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Ciudad:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['ciudad']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Barrio:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['barrio']); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Esposa:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['esposa'] ?: 'No especificado'); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Hijos:</span>
                        <span class="detail-value"><?php echo nl2br(htmlspecialchars($miembro['hijos'] ?: 'No especificado')); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Madre:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['madre'] ?: 'No especificado'); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Padre:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['padre'] ?: 'No especificado'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-briefcase"></i> DATOS LABORALES
                </h2>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Dirección Laboral:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['direccion_laboral']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Empresa:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['empresa'] ?: 'No especificado'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-monument"></i> DATOS LOGIALES
                </h2>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Logia Actual:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['institucion_actual']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Grado Masónico:</span>
                        <span class="detail-value"><?php echo mostrarCampoLogial($miembro['nivel_actual'], 'nivel_actual'); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Grado Capitular:</span>
                        <span class="detail-value"><?php echo mostrarCampoLogial($miembro['nivel_superior'], 'nivel_superior'); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Fecha de Iniciación:</span>
                        <span class="detail-value"><?php echo date('d/m/Y', strtotime($miembro['fecha_ingreso'])); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Logia de Iniciación:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['institucion_ingreso']); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($miembro['documentos'])): ?>
                <div class="detail-row">
                    <div class="detail-item" style="flex: 1 0 100%;">
                        <span class="detail-label">Certificados:</span>
                        <div class="detail-value">
                            <div class="document-preview">
                                <?php
                                $documentos = explode(",", $miembro['documentos']);
                                foreach ($documentos as $documento):
                                    $ext = strtolower(pathinfo($documento, PATHINFO_EXTENSION));
                                    $nombre = basename($documento);
                                    
                                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])):
                                ?>
                                <div class="document-item">
                                    <img src="<?php echo htmlspecialchars($documento); ?>" alt="Documento">
                                    <div class="document-name"><?php echo htmlspecialchars($nombre); ?></div>
                                </div>
                                <?php elseif ($ext == 'pdf'): ?>
                                <div class="document-item">
                                    <i class="fas fa-file-pdf document-icon"></i>
                                    <div class="document-name"><?php echo htmlspecialchars($nombre); ?></div>
                                    <a href="<?php echo htmlspecialchars($documento); ?>" target="_blank" class="btn btn-sm">Ver PDF</a>
                                </div>
                                <?php else: ?>
                                <div class="document-item">
                                    <i class="fas fa-file document-icon"></i>
                                    <div class="document-name"><?php echo htmlspecialchars($nombre); ?></div>
                                    <a href="<?php echo htmlspecialchars($documento); ?>" target="_blank" class="btn btn-sm">Descargar</a>
                                </div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-heartbeat"></i> DATOS MÉDICOS
                </h2>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Grupo Sanguíneo:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['grupo_sanguineo']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Enfermedades Base:</span>
                        <span class="detail-value"><?php echo nl2br(htmlspecialchars($miembro['enfermedades_base'] ?: 'Ninguna')); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">¿Cuenta con Seguro Privado?</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['seguro_privado']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">¿IPS?</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['ips']); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Alergias:</span>
                        <span class="detail-value"><?php echo nl2br(htmlspecialchars($miembro['alergias'] ?: 'Ninguna')); ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Número en caso de Emergencias:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['numero_emergencia']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Contacto en Caso de Emergencias:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($miembro['contacto_emergencia']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="btn-container">
                <a href="list.php?token=<?php echo htmlspecialchars($_GET['token']); ?>" class="btn">
                    <i class="fas fa-arrow-left"></i> Volver a la lista
                </a>
                <a href="edit.php?id=<?php echo $id; ?>&token=<?php echo htmlspecialchars($_GET['token']); ?>" class="btn btn-edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="delete.php?id=<?php echo $id; ?>&token=<?php echo htmlspecialchars($_GET['token']); ?>" class="btn btn-delete" onclick="return confirm('¿Está seguro de que desea eliminar este registro?');">
                    <i class="fas fa-trash"></i> Eliminar
                </a>
            </div>
        </div>
    </div>
</body>
</html>