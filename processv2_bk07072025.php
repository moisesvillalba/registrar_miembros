<?php
// processv2.php - Versión ultra simple para evitar error 500
session_start();

// Mostrar errores para diagnóstico
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>\n";
echo "<html lang='es'>\n";
echo "<head>\n";
echo "<meta charset='UTF-8'>\n";
echo "<title>Proceso de Formulario</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }\n";
echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }\n";
echo ".success { color: #4CAF50; }\n";
echo ".error { color: #F44336; }\n";
echo ".info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }\n";
echo "</style>\n";
echo "</head>\n";
echo "<body>\n";
echo "<div class='container'>\n";

echo "<h1>🔧 Diagnóstico de processv2.php</h1>\n";

// Mostrar información básica
echo "<div class='info'>\n";
echo "<p><strong>✅ El archivo processv2.php funciona</strong></p>\n";
echo "<p><strong>Método recibido:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>\n";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>\n";
echo "<p><strong>Hora:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
echo "</div>\n";

// Si es GET, mostrar diagnóstico
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "<div class='info'>\n";
    echo "<p><strong>⚠️ Acceso via GET</strong></p>\n";
    echo "<p>Este es un acceso de prueba. El formulario debe usar POST.</p>\n";
    echo "</div>\n";

    // Probar conexión a base de datos de forma simple
    try {
        if (file_exists('config/database.php')) {
            echo "<p class='success'>✅ Archivo config/database.php encontrado</p>\n";
            require_once 'config/database.php';
            echo "<p class='success'>✅ config/database.php cargado sin errores</p>\n";

            $database = new Database();
            echo "<p class='success'>✅ Clase Database creada</p>\n";

            $conn = $database->getConnection();
            if ($conn !== null) {
                echo "<p class='success'>✅ Conexión a base de datos: OK</p>\n";
            } else {
                echo "<p class='error'>❌ No se pudo conectar a la base de datos</p>\n";
            }
        } else {
            echo "<p class='error'>❌ Archivo config/database.php no encontrado</p>\n";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar formulario
    echo "<div class='info'>\n";
    echo "<p><strong>📝 Procesando formulario POST</strong></p>\n";
    echo "</div>\n";

    // Mostrar datos recibidos
    echo "<h3>Datos recibidos:</h3>\n";
    echo "<ul>\n";
    foreach ($_POST as $key => $value) {
        echo "<li><strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars($value) . "</li>\n";
    }
    echo "</ul>\n";

    // Validar datos básicos
    $ci = trim($_POST['ci'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');

    if (empty($ci) || empty($nombre) || empty($apellido)) {
        echo "<p class='error'>❌ Faltan datos obligatorios</p>\n";
    } else {
        echo "<p class='success'>✅ Datos básicos completos</p>\n";

        // Intentar guardar en la base de datos
        try {
            require_once 'config/database.php';
            $database = new Database();
            $conn = $database->getConnection();

            if ($conn === null) {
                throw new Exception('No se pudo conectar a la base de datos');
            }

            echo "<p class='success'>✅ Conexión a base de datos OK</p>\n";

            // Obtener todos los datos del formulario
            $ci = trim($_POST['ci'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
            $telefono = trim($_POST['telefono'] ?? '');
            $grupo_sanguineo = $_POST['grupo_sanguineo'] ?? '';

            // Verificar si ya existe el CI
            $stmt = $conn->prepare("SELECT COUNT(*) FROM miembros WHERE ci = ?");
            $stmt->execute([$ci]);

            if ($stmt->fetchColumn() > 0) {
                echo "<p class='error'>❌ Error: Este CI ya está registrado en el sistema</p>\n";
            } else {
                // Insertar nuevo registro
                $sql = "INSERT INTO miembros (ci, nombre, apellido, fecha_nacimiento, telefono, grupo_sanguineo, fecha_registro) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())";

                $stmt = $conn->prepare($sql);
                $resultado = $stmt->execute([$ci, $nombre, $apellido, $fecha_nacimiento, $telefono, $grupo_sanguineo]);

                if ($resultado) {
                    $nuevo_id = $conn->lastInsertId();

                    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0; border: 2px solid #4CAF50;'>\n";
                    echo "<h2 class='success'>🎉 ¡Registro REAL Exitoso!</h2>\n";
                    echo "<p><strong>ID de Registro:</strong> #" . $nuevo_id . "</p>\n";
                    echo "<p><strong>CI:</strong> " . htmlspecialchars($ci) . "</p>\n";
                    echo "<p><strong>Nombre:</strong> " . htmlspecialchars($nombre . ' ' . $apellido) . "</p>\n";
                    echo "<p><strong>Fecha de Nacimiento:</strong> " . htmlspecialchars($fecha_nacimiento) . "</p>\n";
                    echo "<p><strong>Teléfono:</strong> " . htmlspecialchars($telefono) . "</p>\n";
                    echo "<p><strong>Grupo Sanguíneo:</strong> " . htmlspecialchars($grupo_sanguineo) . "</p>\n";
                    echo "<p><strong>Fecha de Registro:</strong> " . date('d/m/Y H:i:s') . "</p>\n";
                    echo "<p class='success'>✅ <strong>Guardado exitosamente en la base de datos</strong></p>\n";
                    echo "</div>\n";
                } else {
                    echo "<p class='error'>❌ Error: No se pudo guardar el registro</p>\n";
                }
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error de base de datos: " . htmlspecialchars($e->getMessage()) . "</p>\n";

            // Mostrar registro simulado como respaldo
            echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
            echo "<h2 style='color: #856404;'>⚠️ Registro Simulado (Error en BD)</h2>\n";
            echo "<p><strong>CI:</strong> " . htmlspecialchars($ci) . "</p>\n";
            echo "<p><strong>Nombre:</strong> " . htmlspecialchars($nombre . ' ' . $apellido) . "</p>\n";
            echo "<p><em>Los datos no se guardaron en la base de datos debido al error anterior.</em></p>\n";
            echo "</div>\n";
        }
    }
}

echo "<div style='margin-top: 30px;'>\n";
echo "<a href='index.php' style='background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>🔙 Volver al formulario</a>\n";
echo "</div>\n";

echo "</div>\n";
echo "</body>\n";
echo "</html>\n";
