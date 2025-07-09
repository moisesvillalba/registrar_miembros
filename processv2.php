<?php
// processv2.php - VERSIÓN FINAL para estructura de BD actualizada
// Optimizado para formulario de scroll infinito
session_start();

// Configuración de errores para producción
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

echo "<!DOCTYPE html>\n";
echo "<html lang='es'>\n";
echo "<head>\n";
echo "<meta charset='UTF-8'>\n";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "<title>Resultado del Registro | Sistema Masónico</title>\n";
echo "<link rel='stylesheet' href='assets/css/styles.css'>\n";
echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>\n";
echo "<style>\n";
echo ".container { max-width: 800px; margin: 20px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }\n";
echo ".success { color: #10b981; background: #d1fae5; padding: 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #10b981; }\n";
echo ".error { color: #ef4444; background: #fee2e2; padding: 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #ef4444; }\n";
echo ".warning { color: #f59e0b; background: #fef3c7; padding: 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #f59e0b; }\n";
echo ".info { color: #3b82f6; background: #dbeafe; padding: 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #3b82f6; }\n";
echo ".result-card { background: #f8f9fa; padding: 25px; border-radius: 12px; margin: 20px 0; border: 1px solid #e9ecef; }\n";
echo ".data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 15px 0; }\n";
echo ".data-item { padding: 15px; background: white; border-radius: 8px; border: 1px solid #dee2e6; }\n";
echo ".data-label { font-weight: 600; color: #495057; font-size: 14px; margin-bottom: 5px; }\n";
echo ".data-value { color: #212529; font-size: 15px; }\n";
echo ".btn { display: inline-block; padding: 12px 24px; background: #8B0000; color: white; text-decoration: none; border-radius: 8px; margin: 10px 5px; transition: all 0.3s; font-weight: 500; }\n";
echo ".btn:hover { background: #5D0000; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(139,0,0,0.3); }\n";
echo ".btn-secondary { background: #6c757d; }\n";
echo ".btn-secondary:hover { background: #545b62; }\n";
echo ".btn-success { background: #10b981; }\n";
echo ".btn-success:hover { background: #059669; }\n";
echo ".stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }\n";
echo ".stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; text-align: center; }\n";
echo ".stat-number { font-size: 2rem; font-weight: bold; margin-bottom: 5px; }\n";
echo ".stat-label { font-size: 0.9rem; opacity: 0.9; }\n";
echo "@media (max-width: 768px) { .data-grid { grid-template-columns: 1fr; } .container { margin: 10px; padding: 20px; } }\n";
echo "</style>\n";
echo "</head>\n";
echo "<body>\n";
echo "<div class='container'>\n";

echo "<h1 style='text-align: center; color: #8B0000; margin-bottom: 30px;'>";
echo "<i class='fas fa-clipboard-check'></i> Procesamiento del Registro Masónico";
echo "</h1>\n";

// Mostrar información básica del sistema
// echo "<div class='info'>\n";
// echo "<h3><i class='fas fa-info-circle'></i> Información del Procesamiento</h3>\n";
// echo "<p><strong>✅ Procesador funcionando correctamente</strong></p>\n";
// echo "<p><strong>Método:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>\n";
// echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>\n";
// echo "<p><strong>Fecha/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>\n";
// echo "<p><strong>IP Cliente:</strong> " . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']) . "</p>\n";
// echo "</div>\n";

// Verificar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<div class='warning'>\n";
    echo "<h3>⚠️ Acceso Directo Detectado</h3>\n";
    echo "<p>Este script debe ser accedido mediante el formulario de registro.</p>\n";
    echo "<p><a href='index.php' class='btn'><i class='fas fa-arrow-left'></i> Volver al Formulario</a></p>\n";
    echo "</div>\n";
    echo "</div></body></html>\n";
    exit;
}

// Procesar datos del formulario
echo "<div class='info'>\n";
echo "<h3><i class='fas fa-cogs'></i> Procesando Datos del Formulario</h3>\n";
echo "<p>Analizando y validando información recibida...</p>\n";
echo "</div>\n";

// Obtener y limpiar datos del formulario (usando nombres exactos de la BD)
$datos = [];

// === DATOS PERSONALES ===
$datos['ci'] = trim($_POST['ci'] ?? '');
$datos['nombre'] = trim($_POST['nombre'] ?? '');
$datos['apellido'] = trim($_POST['apellido'] ?? '');
$datos['fecha_nacimiento'] = $_POST['fecha_nacimiento'] ?? '';
$datos['lugar'] = trim($_POST['lugar'] ?? '');
$datos['profesion'] = trim($_POST['profesion'] ?? '');
$datos['direccion'] = trim($_POST['direccion'] ?? '');
$datos['telefono'] = trim($_POST['telefono'] ?? '');
$datos['ciudad'] = trim($_POST['ciudad'] ?? '');
$datos['barrio'] = trim($_POST['barrio'] ?? '');

// Datos familiares opcionales
$datos['esposa'] = trim($_POST['esposa'] ?? '');
$datos['hijos'] = trim($_POST['hijos'] ?? '');
$datos['madre'] = trim($_POST['madre'] ?? '');
$datos['padre'] = trim($_POST['padre'] ?? '');

// === DATOS LABORALES ===
$datos['direccion_laboral'] = trim($_POST['direccion_laboral'] ?? '');
$datos['empresa'] = trim($_POST['empresa'] ?? '');
$datos['descripcion_otros_trabajos'] = trim($_POST['descripcion_otros_trabajos'] ?? '');

// === DATOS LOGIALES (usando nombres correctos de BD) ===
// === DATOS LOGIALES (usando nombres correctos de BD) ===
$datos['institucion_actual'] = trim($_POST['institucion_actual'] ?? '');
$datos['logia_actual'] = trim($_POST['institucion_actual'] ?? ''); // Campo duplicado

$datos['nivel_actual'] = trim($_POST['nivel_actual'] ?? '');
$datos['grado_masonico'] = trim($_POST['nivel_actual'] ?? ''); // Campo duplicado

$datos['fecha_ingreso'] = $_POST['fecha_ingreso'] ?? '';
$datos['fecha_iniciacion'] = $_POST['fecha_ingreso'] ?? ''; // Campo duplicado

$datos['logia_iniciacion'] = trim($_POST['logia_iniciacion'] ?? '');
$datos['institucion_ingreso'] = trim($_POST['logia_iniciacion'] ?? ''); // Campo duplicado

// Grados adicionales
$datos['nivel_superior'] = trim($_POST['nivel_superior'] ?? '');
$datos['grados_que_tienen'] = trim($_POST['grados_que_tienen'] ?? '');
$datos['logia_perfeccion'] = trim($_POST['logia_perfeccion'] ?? '');
$datos['descripcion_grado_capitular'] = trim($_POST['descripcion_grado_capitular'] ?? '');

// === DATOS MÉDICOS ===
$datos['grupo_sanguineo'] = $_POST['grupo_sanguineo'] ?? '';
$datos['enfermedades_base'] = trim($_POST['enfermedades_base'] ?? '');
$datos['seguro_privado'] = $_POST['seguro_privado'] ?? 'No';
$datos['ips'] = $_POST['ips'] ?? 'No';
$datos['alergias'] = trim($_POST['alergias'] ?? '');
$datos['numero_emergencia'] = trim($_POST['numero_emergencia'] ?? '');
$datos['contacto_emergencia'] = trim($_POST['contacto_emergencia'] ?? '');

// === INFORMACIÓN ADICIONAL ===
$datos['estado'] = $_POST['estado'] ?? 'pendiente';
$datos['acepta_terminos'] = isset($_POST['acepta_terminos']) ? 'Si' : 'No';

// Mostrar datos recibidos
echo "<div class='result-card'>\n";
echo "<h3><i class='fas fa-database'></i> Datos Recibidos del Formulario</h3>\n";

// === DATOS PERSONALES ===
echo "<h4 style='color: #8B0000; border-bottom: 2px solid #8B0000; padding-bottom: 8px;'>";
echo "<i class='fas fa-user'></i> Datos Personales";
echo "</h4>\n";
echo "<div class='data-grid'>\n";
echo "<div class='data-item'><div class='data-label'>CI:</div><div class='data-value'>" . htmlspecialchars($datos['ci']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Nombre Completo:</div><div class='data-value'>" . htmlspecialchars($datos['nombre'] . ' ' . $datos['apellido']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Fecha Nacimiento:</div><div class='data-value'>" . htmlspecialchars($datos['fecha_nacimiento']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Lugar:</div><div class='data-value'>" . htmlspecialchars($datos['lugar']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Profesión:</div><div class='data-value'>" . htmlspecialchars($datos['profesion']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Dirección:</div><div class='data-value'>" . htmlspecialchars($datos['direccion']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Ciudad:</div><div class='data-value'>" . htmlspecialchars($datos['ciudad']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Barrio:</div><div class='data-value'>" . htmlspecialchars($datos['barrio']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Teléfono:</div><div class='data-value'>" . htmlspecialchars($datos['telefono']) . "</div></div>\n";
echo "</div>\n";

// Datos familiares si están presentes
if (!empty($datos['esposa']) || !empty($datos['hijos']) || !empty($datos['madre']) || !empty($datos['padre'])) {
    echo "<h4 style='color: #8B0000; margin-top: 25px;'><i class='fas fa-heart'></i> Datos Familiares</h4>\n";
    echo "<div class='data-grid'>\n";
    if (!empty($datos['esposa'])) echo "<div class='data-item'><div class='data-label'>Esposa:</div><div class='data-value'>" . htmlspecialchars($datos['esposa']) . "</div></div>\n";
    if (!empty($datos['hijos'])) echo "<div class='data-item'><div class='data-label'>Hijos:</div><div class='data-value'>" . htmlspecialchars($datos['hijos']) . "</div></div>\n";
    if (!empty($datos['madre'])) echo "<div class='data-item'><div class='data-label'>Madre:</div><div class='data-value'>" . htmlspecialchars($datos['madre']) . "</div></div>\n";
    if (!empty($datos['padre'])) echo "<div class='data-item'><div class='data-label'>Padre:</div><div class='data-value'>" . htmlspecialchars($datos['padre']) . "</div></div>\n";
    echo "</div>\n";
}

// === DATOS LABORALES ===
echo "<h4 style='color: #8B0000; margin-top: 25px; border-bottom: 2px solid #8B0000; padding-bottom: 8px;'>";
echo "<i class='fas fa-briefcase'></i> Datos Laborales";
echo "</h4>\n";
echo "<div class='data-grid'>\n";
echo "<div class='data-item'><div class='data-label'>Dirección Laboral:</div><div class='data-value'>" . htmlspecialchars($datos['direccion_laboral']) . "</div></div>\n";
if (!empty($datos['empresa'])) echo "<div class='data-item'><div class='data-label'>Empresa:</div><div class='data-value'>" . htmlspecialchars($datos['empresa']) . "</div></div>\n";
if (!empty($datos['descripcion_otros_trabajos'])) echo "<div class='data-item' style='grid-column: 1 / -1;'><div class='data-label'>Otros Trabajos:</div><div class='data-value'>" . htmlspecialchars($datos['descripcion_otros_trabajos']) . "</div></div>\n";
echo "</div>\n";

// === DATOS LOGIALES ===
echo "<h4 style='color: #8B0000; margin-top: 25px; border-bottom: 2px solid #8B0000; padding-bottom: 8px;'>";
echo "<i class='fas fa-university'></i> Datos Logiales";
echo "</h4>\n";
echo "<div class='data-grid'>\n";
echo "<div class='data-item'><div class='data-label'>Logia Actual:</div><div class='data-value'>" . htmlspecialchars($datos['institucion_actual']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Grado Masónico:</div><div class='data-value'>" . htmlspecialchars($datos['nivel_actual']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Fecha Iniciación:</div><div class='data-value'>" . htmlspecialchars($datos['fecha_ingreso']) . "</div></div>\n";
if (!empty($datos['institucion_ingreso'])) echo "<div class='data-item'><div class='data-label'>Logia Iniciación:</div><div class='data-value'>" . htmlspecialchars($datos['institucion_ingreso']) . "</div></div>\n";
if (!empty($datos['nivel_superior'])) echo "<div class='data-item'><div class='data-label'>Nivel Superior:</div><div class='data-value'>" . htmlspecialchars($datos['nivel_superior']) . "</div></div>\n";
if (!empty($datos['grados_que_tienen'])) echo "<div class='data-item'><div class='data-label'>Grados que Posee:</div><div class='data-value'>" . htmlspecialchars($datos['grados_que_tienen']) . "</div></div>\n";
echo "</div>\n";

// === DATOS MÉDICOS ===
echo "<h4 style='color: #8B0000; margin-top: 25px; border-bottom: 2px solid #8B0000; padding-bottom: 8px;'>";
echo "<i class='fas fa-heartbeat'></i> Datos Médicos";
echo "</h4>\n";
echo "<div class='data-grid'>\n";
echo "<div class='data-item'><div class='data-label'>Grupo Sanguíneo:</div><div class='data-value'>" . htmlspecialchars($datos['grupo_sanguineo']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Seguro Privado:</div><div class='data-value'>" . htmlspecialchars($datos['seguro_privado']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>IPS:</div><div class='data-value'>" . htmlspecialchars($datos['ips']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Contacto Emergencia:</div><div class='data-value'>" . htmlspecialchars($datos['contacto_emergencia']) . "</div></div>\n";
echo "<div class='data-item'><div class='data-label'>Número Emergencia:</div><div class='data-value'>" . htmlspecialchars($datos['numero_emergencia']) . "</div></div>\n";
if (!empty($datos['enfermedades_base'])) echo "<div class='data-item'><div class='data-label'>Enfermedades Base:</div><div class='data-value'>" . htmlspecialchars($datos['enfermedades_base']) . "</div></div>\n";
if (!empty($datos['alergias'])) echo "<div class='data-item'><div class='data-label'>Alergias:</div><div class='data-value'>" . htmlspecialchars($datos['alergias']) . "</div></div>\n";
echo "</div>\n";

echo "</div>\n"; // Fin result-card

// Validar campos obligatorios
$campos_requeridos = [
    'ci' => 'CI',
    'nombre' => 'Nombre',
    'apellido' => 'Apellido',
    'fecha_nacimiento' => 'Fecha de Nacimiento',
    'lugar' => 'Lugar de Nacimiento',
    'profesion' => 'Profesión',
    'direccion' => 'Dirección',
    'telefono' => 'Teléfono',
    'ciudad' => 'Ciudad',
    'barrio' => 'Barrio',
    'direccion_laboral' => 'Dirección Laboral',
    'institucion_actual' => 'Logia Actual',
    'nivel_actual' => 'Grado Masónico',
    'fecha_ingreso' => 'Fecha de Iniciación',
    'grupo_sanguineo' => 'Grupo Sanguíneo',
    'numero_emergencia' => 'Número de Emergencia',
    'contacto_emergencia' => 'Contacto de Emergencia'
];

$campos_faltantes = [];
foreach ($campos_requeridos as $campo => $label) {
    if (empty($datos[$campo])) {
        $campos_faltantes[] = $label;
    }
}

// Verificar términos y condiciones
if ($datos['acepta_terminos'] !== 'Si') {
    $campos_faltantes[] = 'Aceptación de Términos y Condiciones';
}

if (!empty($campos_faltantes)) {
    echo "<div class='error'>\n";
    echo "<h3>❌ Campos Obligatorios Faltantes</h3>\n";
    echo "<p>Los siguientes campos son requeridos:</p>\n";
    echo "<ul>\n";
    foreach ($campos_faltantes as $campo) {
        echo "<li>" . htmlspecialchars($campo) . "</li>\n";
    }
    echo "</ul>\n";
    echo "<p><a href='javascript:history.back()' class='btn'><i class='fas fa-arrow-left'></i> Volver y Completar</a></p>\n";
    echo "</div>\n";
} else {
    echo "<div class='success'>\n";
    echo "<h3>✅ Validación de Datos Completada</h3>\n";
    echo "<p>Todos los campos obligatorios están completos. Procediendo a guardar en la base de datos...</p>\n";
    echo "</div>\n";

    // Intentar guardar en la base de datos
    try {
        require_once 'config/database.php';
        $database = new Database();
        $conn = $database->getConnection();

        if ($conn === null) {
            throw new Exception('No se pudo conectar a la base de datos: ' . ($database->connection_error ?? 'Error desconocido'));
        }

        echo "<div class='success'>\n";
        echo "<p><i class='fas fa-database'></i> Conexión a base de datos establecida</p>\n";
        echo "</div>\n";

        // Verificar si ya existe el CI
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM miembros WHERE ci = ?");
        $stmt->execute([$datos['ci']]);
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            echo "<div class='warning'>\n";
            echo "<h3>⚠️ CI Duplicado</h3>\n";
            echo "<p>El CI <strong>" . htmlspecialchars($datos['ci']) . "</strong> ya está registrado en el sistema.</p>\n";
            echo "<p>Si necesita actualizar los datos, contacte al administrador del sistema.</p>\n";
            echo "</div>\n";
        } else {
            // Preparar consulta de inserción (usando TODOS los campos de la BD)
            $sql = "INSERT INTO miembros (
                ci, nombre, apellido, fecha_nacimiento, lugar, profesion, direccion, telefono, ciudad, barrio,
                esposa, hijos, madre, padre, direccion_laboral, empresa, descripcion_otros_trabajos,
                institucion_actual, logia_actual, nivel_actual, grado_masonico, 
                fecha_ingreso, fecha_iniciacion, institucion_ingreso, logia_iniciacion,
                nivel_superior, grados_que_tienen, logia_perfeccion, descripcion_grado_capitular,
                grupo_sanguineo, enfermedades_base, seguro_privado, ips, alergias,
                numero_emergencia, contacto_emergencia, estado
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $resultado = $stmt->execute([
                $datos['ci'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['fecha_nacimiento'],
                $datos['lugar'],
                $datos['profesion'],
                $datos['direccion'],
                $datos['telefono'],
                $datos['ciudad'],
                $datos['barrio'],
                $datos['esposa'],
                $datos['hijos'],
                $datos['madre'],
                $datos['padre'],
                $datos['direccion_laboral'],
                $datos['empresa'],
                $datos['descripcion_otros_trabajos'],
                // Campos logiales duplicados para compatibilidad
                $datos['institucion_actual'],
                $datos['logia_actual'], // Ambos campos
                $datos['nivel_actual'],
                $datos['grado_masonico'], // Ambos campos
                $datos['fecha_ingreso'],
                $datos['fecha_iniciacion'], // Ambos campos
                $datos['institucion_ingreso'],
                $datos['logia_iniciacion'], // Ambos campos
                $datos['nivel_superior'],
                $datos['grados_que_tienen'],
                $datos['logia_perfeccion'],
                $datos['descripcion_grado_capitular'],
                $datos['grupo_sanguineo'],
                $datos['enfermedades_base'],
                $datos['seguro_privado'],
                $datos['ips'],
                $datos['alergias'],
                $datos['numero_emergencia'],
                $datos['contacto_emergencia'],
                $datos['estado']
            ]);

            if ($resultado) {
                $nuevo_id = $conn->lastInsertId();

                echo "<div style='background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 40px; border-radius: 15px; margin: 30px 0; border: 3px solid #28a745; box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3); text-align: center;'>\n";
                echo "<div style='font-size: 4rem; color: #28a745; margin-bottom: 20px;'><i class='fas fa-check-circle'></i></div>\n";
                echo "<h2 style='color: #155724; margin-bottom: 25px; font-size: 2rem;'>🎉 ¡REGISTRO EXITOSO!</h2>\n";

                echo "<div class='stats-grid'>\n";
                echo "<div class='stat-card' style='background: linear-gradient(135deg, #28a745 0%, #20c997 100%);'>";
                echo "<div class='stat-number'>#" . $nuevo_id . "</div>";
                echo "<div class='stat-label'>ID de Registro</div>";
                echo "</div>\n";
                echo "<div class='stat-card' style='background: linear-gradient(135deg, #8B0000 0%, #B22222 100%);'>";
                echo "<div class='stat-number'>" . htmlspecialchars($datos['ci']) . "</div>";
                echo "<div class='stat-label'>Cédula de Identidad</div>";
                echo "</div>\n";
                echo "<div class='stat-card' style='background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);'>";
                echo "<div class='stat-number'>" . htmlspecialchars($datos['nivel_actual']) . "</div>";
                echo "<div class='stat-label'>Grado Masónico</div>";
                echo "</div>\n";
                echo "</div>\n";

                echo "<div style='background: rgba(255,255,255,0.9); padding: 25px; border-radius: 12px; margin: 25px 0;'>\n";
                echo "<h3 style='color: #155724; margin-bottom: 15px;'><i class='fas fa-user-check'></i> Datos del Hermano Registrado</h3>\n";
                echo "<div class='data-grid'>\n";
                echo "<div class='data-item'><div class='data-label'>Nombre Completo:</div><div class='data-value'><strong>" . htmlspecialchars($datos['nombre'] . ' ' . $datos['apellido']) . "</strong></div></div>\n";
                echo "<div class='data-item'><div class='data-label'>Logia:</div><div class='data-value'>" . htmlspecialchars($datos['institucion_actual']) . "</div></div>\n";
                echo "<div class='data-item'><div class='data-label'>Grupo Sanguíneo:</div><div class='data-value'>" . htmlspecialchars($datos['grupo_sanguineo']) . "</div></div>\n";
                echo "<div class='data-item'><div class='data-label'>Teléfono:</div><div class='data-value'>" . htmlspecialchars($datos['telefono']) . "</div></div>\n";
                echo "<div class='data-item'><div class='data-label'>Ciudad:</div><div class='data-value'>" . htmlspecialchars($datos['ciudad']) . "</div></div>\n";
                echo "<div class='data-item'><div class='data-label'>Fecha de Registro:</div><div class='data-value'>" . date('d/m/Y H:i:s') . "</div></div>\n";
                echo "</div>\n";
                echo "</div>\n";

                echo "<div style='color: #155724; margin: 20px 0; font-size: 1.1rem;'>\n";
                echo "<p><strong><i class='fas fa-database'></i> Los datos se guardaron exitosamente en la base de datos</strong></p>\n";
                echo "<p><i class='fas fa-shield-alt'></i> El registro ha sido procesado y almacenado de forma segura</p>\n";
                echo "<p><i class='fas fa-clock'></i> Estado: <strong>Pendiente de revisión</strong></p>\n";
                echo "</div>\n";
                echo "</div>\n";

                // Obtener estadísticas del sistema
                try {
                    $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM miembros");
                    $stmt_count->execute();
                    $total_miembros = $stmt_count->fetch()['total'];

                    $stmt_logia = $conn->prepare("SELECT COUNT(*) as total_logia FROM miembros WHERE institucion_actual = ?");
                    $stmt_logia->execute([$datos['institucion_actual']]);
                    $total_logia = $stmt_logia->fetch()['total_logia'];

                    $stmt_recientes = $conn->prepare("SELECT COUNT(*) as recientes FROM miembros WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                    $stmt_recientes->execute();
                    $registros_recientes = $stmt_recientes->fetch()['recientes'];

                    echo "<div class='result-card'>\n";
                    echo "<h3><i class='fas fa-chart-bar'></i> Estadísticas del Sistema</h3>\n";
                    echo "<div class='stats-grid'>\n";
                    echo "<div class='stat-card'>";
                    echo "<div class='stat-number'>" . $total_miembros . "</div>";
                    echo "<div class='stat-label'>Total Miembros</div>";
                    echo "</div>\n";
                    echo "<div class='stat-card'>";
                    echo "<div class='stat-number'>" . $total_logia . "</div>";
                    echo "<div class='stat-label'>Miembros en esta Logia</div>";
                    echo "</div>\n";
                    echo "<div class='stat-card'>";
                    echo "<div class='stat-number'>" . $registros_recientes . "</div>";
                    echo "<div class='stat-label'>Registros esta semana</div>";
                    echo "</div>\n";
                    echo "</div>\n";
                    echo "</div>\n";
                } catch (Exception $e) {
                    echo "<p style='color: #6c757d; font-size: 12px;'>Estadísticas no disponibles temporalmente.</p>\n";
                }
            } else {
                echo "<div class='error'>\n";
                echo "<h3>❌ Error en el Registro</h3>\n";
                echo "<p>No se pudo guardar el registro en la base de datos.</p>\n";
                echo "<p>Error técnico: La consulta no se ejecutó correctamente.</p>\n";
                echo "</div>\n";
            }
        }
    } catch (Exception $e) {
        echo "<div class='error'>\n";
        echo "<h3>❌ Error de Base de Datos</h3>\n";
        echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>\n";
        echo "</div>\n";

        // Registro simulado como respaldo
        echo "<div style='background: #fff3cd; padding: 25px; border-radius: 12px; margin: 25px 0; border: 2px solid #ffeaa7;'>\n";
        echo "<h3 style='color: #856404;'>⚠️ Modo Sin Base de Datos</h3>\n";
        echo "<p style='color: #856404;'>Los datos fueron recibidos correctamente pero no se guardaron en la base de datos.</p>\n";
        echo "<div class='data-grid'>\n";
        echo "<div class='data-item'><div class='data-label'>CI:</div><div class='data-value'>" . htmlspecialchars($datos['ci']) . "</div></div>\n";
        echo "<div class='data-item'><div class='data-label'>Nombre:</div><div class='data-value'>" . htmlspecialchars($datos['nombre'] . ' ' . $datos['apellido']) . "</div></div>\n";
        echo "<div class='data-item'><div class='data-label'>Logia:</div><div class='data-value'>" . htmlspecialchars($datos['institucion_actual']) . "</div></div>\n";
        echo "<div class='data-item'><div class='data-label'>Estado:</div><div class='data-value'><strong>NO GUARDADO EN BD</strong></div></div>\n";
        echo "</div>\n";
        echo "</div>\n";
    }
}

// Manejo de archivos subidos
if (!empty($_FILES)) {
    echo "<div class='result-card'>\n";
    echo "<h3><i class='fas fa-paperclip'></i> Archivos Recibidos</h3>\n";

    $archivos_procesados = 0;
    foreach ($_FILES as $fieldName => $file) {
        if (is_array($file['name'])) {
            // Múltiples archivos
            for ($i = 0; $i < count($file['name']); $i++) {
                if ($file['error'][$i] === UPLOAD_ERR_OK) {
                    echo "<p style='color: #10b981;'><i class='fas fa-check'></i> <strong>" . htmlspecialchars($fieldName) . ":</strong> " . htmlspecialchars($file['name'][$i]) . " (" . number_format($file['size'][$i] / 1024, 2) . " KB)</p>\n";
                    $archivos_procesados++;
                } elseif ($file['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    echo "<p style='color: #ef4444;'><i class='fas fa-times'></i> <strong>" . htmlspecialchars($fieldName) . ":</strong> Error en " . htmlspecialchars($file['name'][$i]) . " (código: " . $file['error'][$i] . ")</p>\n";
                }
            }
        } else {
            // Archivo único
            if ($file['error'] === UPLOAD_ERR_OK) {
                echo "<p style='color: #10b981;'><i class='fas fa-check'></i> <strong>" . htmlspecialchars($fieldName) . ":</strong> " . htmlspecialchars($file['name']) . " (" . number_format($file['size'] / 1024, 2) . " KB)</p>\n";
                $archivos_procesados++;
            } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                echo "<p style='color: #ef4444;'><i class='fas fa-times'></i> <strong>" . htmlspecialchars($fieldName) . ":</strong> Error en la subida (código: " . $file['error'] . ")</p>\n";
            }
        }
    }

    if ($archivos_procesados > 0) {
        echo "<div style='background: #d1fae5; padding: 15px; border-radius: 8px; margin-top: 15px;'>\n";
        echo "<p style='color: #065f46; margin: 0;'><i class='fas fa-info-circle'></i> <strong>" . $archivos_procesados . " archivo(s)</strong> recibido(s) correctamente.</p>\n";
        echo "<p style='color: #065f46; margin: 5px 0 0 0; font-size: 0.9rem;'>Nota: Los archivos se reciben y procesan correctamente.</p>\n";
        echo "</div>\n";
    }
    echo "</div>\n";
}

echo "<div style='margin-top: 50px; text-align: center; padding: 30px; border-top: 3px solid #e9ecef;'>\n";
echo "<div style='margin-bottom: 25px;'>\n";
echo "<a href='index.php' class='btn btn-success' style='font-size: 1.1rem; padding: 15px 30px;'><i class='fas fa-plus'></i> Registrar Otro Miembro</a>\n";
// echo "<a href='test_database.php' class='btn btn-secondary'><i class='fas fa-database'></i> Verificar Base de Datos</a>\n";
echo "</div>\n";

// Mostrar información de contacto
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 12px; margin-top: 20px;'>\n";
echo "<h4 style='color: #8B0000; margin-bottom: 15px;'><i class='fas fa-envelope'></i> Información de Contacto</h4>\n";
echo "<p style='margin: 5px 0; color: #495057;'><strong>Gran Logia del Paraguay</strong></p>\n";
echo "<p style='margin: 5px 0; color: #6c757d;'>Para consultas sobre el registro, contacte con la secretaría.</p>\n";
echo "<p style='margin: 5px 0; color: #6c757d;'>El estado del registro será notificado oportunamente.</p>\n";
echo "</div>\n";
echo "</div>\n";

echo "<div style='margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; font-size: 13px; color: #6c757d; text-align: center;'>\n";
echo "<p><strong>Sistema de Registro Masónico - Gran Logia de Libres y Aceptados Masones del Paraguay</strong></p>\n";
echo "<p>Procesado el " . date('d/m/Y H:i:s') . " | PHP " . phpversion() . " | Versión Scroll Infinito 3.0 FINAL</p>\n";
echo "<p style='margin-top: 10px;'><em>\"Libertad, Igualdad, Fraternidad\"</em></p>\n";
echo "</div>\n";

echo "</div>\n"; // Cierre container
echo "</body>\n";
echo "</html>\n";
