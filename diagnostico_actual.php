<?php
// ============================================================================
// DIAGNÓSTICO DEL SISTEMA ACTUAL
// diagnostico_actual.php
// ============================================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico del Sistema Actual</h1>";
echo "<p>Análisis para migrar al sistema masónico completo</p>";
echo "<hr>";

// 1. Verificar estructura actual de la tabla
echo "<h2>📊 1. Estructura Actual de la Base de Datos</h2>";
try {
    if (file_exists('config/database.php')) {
        include_once 'config/database.php';
        
        if (class_exists('Database')) {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                echo "<p style='color: green;'>✓ Conexión exitosa</p>";
                
                // Obtener estructura actual
                $stmt = $db->query("DESCRIBE miembros");
                echo "<h3>Campos Existentes:</h3>";
                echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por Defecto</th></tr>";
                
                $campos_actuales = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $campos_actuales[] = $row['Field'];
                    echo "<tr>";
                    echo "<td><strong>" . $row['Field'] . "</strong></td>";
                    echo "<td>" . $row['Type'] . "</td>";
                    echo "<td>" . ($row['Null'] === 'YES' ? 'Sí' : 'No') . "</td>";
                    echo "<td>" . $row['Key'] . "</td>";
                    echo "<td>" . ($row['Default'] ?: 'NULL') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                // Contar registros
                $stmt = $db->query("SELECT COUNT(*) as total FROM miembros");
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                echo "<p><strong>Total de registros actuales: $total</strong></p>";
                
            } else {
                echo "<p style='color: red;'>✗ No se pudo conectar a la base de datos</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Clase Database no encontrada</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Archivo config/database.php no encontrado</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// 2. Verificar archivos existentes
echo "<h2>📁 2. Archivos Existentes</h2>";
$archivos_esperados = [
    'index.php' => 'Formulario principal',
    'list.php' => 'Lista de miembros',
    'ver_registro.php' => 'Ver detalles',
    'editar_registro.php' => 'Editar registro',
    'login.php' => 'Página de login',
    'process.php' => 'Procesamiento de formulario',
    'config/database.php' => 'Configuración de BD',
    'includes/flash.php' => 'Sistema de mensajes',
    'assets/css/styles.css' => 'Estilos CSS',
    'assets/js/script.js' => 'JavaScript'
];

foreach ($archivos_esperados as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        $tamaño = filesize($archivo);
        echo "<p style='color: green;'>✓ <strong>$archivo</strong> - $descripcion (" . number_format($tamaño) . " bytes)</p>";
    } else {
        echo "<p style='color: red;'>✗ <strong>$archivo</strong> - $descripcion (NO EXISTE)</p>";
    }
}

echo "<hr>";

// 3. Verificar tabla usuarios
echo "<h2>👥 3. Sistema de Usuarios</h2>";
try {
    if (isset($db)) {
        $stmt = $db->query("SHOW TABLES LIKE 'usuarios'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✓ Tabla 'usuarios' existe</p>";
            
            // Mostrar estructura
            $stmt = $db->query("DESCRIBE usuarios");
            echo "<h4>Estructura de tabla usuarios:</h4>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
            }
            echo "</table>";
            
            // Contar usuarios
            $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
            $total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            echo "<p>Total de usuarios: <strong>$total_usuarios</strong></p>";
            
        } else {
            echo "<p style='color: red;'>✗ Tabla 'usuarios' NO existe</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠ Error verificando usuarios: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// 4. Campos faltantes según el prompt
echo "<h2>🆕 4. Campos Nuevos Requeridos</h2>";
$campos_nuevos = [
    'foto_hermano' => 'VARCHAR(255) - Ruta de la foto del hermano',
    'descripcion_otros_trabajos' => 'TEXT - Descripción de trabajos adicionales',
    'grados_que_tienen' => 'TEXT - Lista de grados masónicos',
    'logia_perfeccion' => 'VARCHAR(150) - Nombre de Logia de Perfección',
    'descripcion_grado_capitular' => 'TEXT - Descripción detallada del grado capitular'
];

echo "<p>Campos que necesitan ser agregados:</p>";
foreach ($campos_nuevos as $campo => $descripcion) {
    if (isset($campos_actuales) && in_array($campo, $campos_actuales)) {
        echo "<p style='color: green;'>✓ <strong>$campo</strong> - $descripcion (YA EXISTE)</p>";
    } else {
        echo "<p style='color: orange;'>⚠ <strong>$campo</strong> - $descripcion (FALTA AGREGAR)</p>";
    }
}

echo "<hr>";

// 5. Verificar carpetas de uploads
echo "<h2>📂 5. Carpetas de Archivos</h2>";
$carpetas = [
    'uploads' => 'Carpeta principal de uploads',
    'uploads/fotos' => 'Fotos de hermanos',
    'uploads/documentos' => 'Documentos y certificados'
];

foreach ($carpetas as $carpeta => $descripcion) {
    if (is_dir($carpeta)) {
        $permisos = substr(sprintf('%o', fileperms($carpeta)), -4);
        echo "<p style='color: green;'>✓ <strong>$carpeta</strong> - $descripcion (Permisos: $permisos)</p>";
    } else {
        echo "<p style='color: red;'>✗ <strong>$carpeta</strong> - $descripcion (NO EXISTE)</p>";
    }
}

echo "<hr>";

// 6. Generar script de migración
echo "<h2>🔧 6. Script de Migración Generado</h2>";
echo "<p>Basado en el análisis, aquí está el SQL para actualizar tu base de datos:</p>";

echo "<div style='background: #f8f8f8; padding: 15px; border: 1px solid #ddd; font-family: monospace;'>";
echo "<h4>SQL para ejecutar en phpMyAdmin:</h4>";
echo "<pre>";

// Generar SQL dinámicamente
$sql_migracion = "-- Script de migración generado automáticamente\n";
$sql_migracion .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";

$sql_migracion .= "USE sistema_registro;\n\n";

// Agregar campos faltantes
foreach ($campos_nuevos as $campo => $definicion) {
    if (!isset($campos_actuales) || !in_array($campo, $campos_actuales)) {
        $tipo = explode(' - ', $definicion)[0];
        $sql_migracion .= "ALTER TABLE miembros ADD COLUMN $campo $tipo;\n";
    }
}

// Crear tabla usuarios si no existe
$sql_migracion .= "\n-- Crear tabla usuarios si no existe\n";
$sql_migracion .= "CREATE TABLE IF NOT EXISTS usuarios (\n";
$sql_migracion .= "    id INT AUTO_INCREMENT PRIMARY KEY,\n";
$sql_migracion .= "    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,\n";
$sql_migracion .= "    password_hash VARCHAR(255) NOT NULL,\n";
$sql_migracion .= "    nombre_completo VARCHAR(100) NOT NULL,\n";
$sql_migracion .= "    email VARCHAR(100) UNIQUE NOT NULL,\n";
$sql_migracion .= "    rol ENUM('admin', 'editor', 'visualizador') DEFAULT 'visualizador',\n";
$sql_migracion .= "    activo BOOLEAN DEFAULT TRUE,\n";
$sql_migracion .= "    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
$sql_migracion .= "    ultima_conexion TIMESTAMP NULL\n";
$sql_migracion .= ");\n\n";

// Insertar usuario admin por defecto
$sql_migracion .= "-- Insertar usuario admin por defecto\n";
$sql_migracion .= "INSERT IGNORE INTO usuarios (nombre_usuario, password_hash, nombre_completo, email, rol) VALUES\n";
$sql_migracion .= "('admin', '\$2y\$10\$8K5g7G5g7G5g7G5g7G5g7uFGHJKLMNOPQRSTUVWXYZ', 'Administrador', 'admin@sistema.com', 'admin');\n\n";

$sql_migracion .= "-- Verificar cambios\n";
$sql_migracion .= "DESCRIBE miembros;\n";
$sql_migracion .= "DESCRIBE usuarios;\n";

echo htmlspecialchars($sql_migracion);
echo "</pre>";
echo "</div>";

echo "<hr>";

// 7. Pasos siguientes
echo "<h2>📋 7. Pasos Siguientes</h2>";
echo "<ol>";
echo "<li><strong>Hacer backup</strong> de tu base de datos actual</li>";
echo "<li><strong>Crear las carpetas</strong> faltantes (uploads/fotos, uploads/documentos)</li>";
echo "<li><strong>Ejecutar el SQL</strong> de migración en phpMyAdmin</li>";
echo "<li><strong>Actualizar archivos PHP</strong> con las nuevas funcionalidades</li>";
echo "<li><strong>Configurar sistema de usuarios</strong> y permisos</li>";
echo "<li><strong>Probar funcionalidades</strong> nuevas</li>";
echo "</ol>";

echo "<hr>";

// 8. Archivos que necesitan actualización
echo "<h2>📝 8. Archivos que Necesitan Actualización</h2>";
$actualizaciones = [
    'index.php' => 'Agregar campos nuevos al formulario',
    'process.php' => 'Procesar nuevos campos y archivos',
    'list.php' => 'Mostrar campos adicionales',
    'ver_registro.php' => 'Mostrar información completa',
    'editar_registro.php' => 'Editar campos nuevos',
    'login.php' => 'Sistema de autenticación robusto',
    'config/form_settings.php' => 'Configuración flexible (NUEVO)',
    'manage_users.php' => 'Gestión de usuarios (NUEVO)',
    'admin_config.php' => 'Configuración de campos (NUEVO)',
    'assets/css/styles.css' => 'Estilos para nuevos componentes',
    'assets/js/script.js' => 'JavaScript para nuevas funcionalidades'
];

foreach ($actualizaciones as $archivo => $descripcion) {
    $existe = file_exists($archivo) ? '✓' : '✗';
    $color = file_exists($archivo) ? 'green' : 'red';
    echo "<p style='color: $color;'>$existe <strong>$archivo</strong> - $descripcion</p>";
}

echo "<hr>";

// 9. Resumen final
echo "<h2>📊 9. Resumen de Migración</h2>";
echo "<div style='background: #e8f4f8; padding: 15px; border-radius: 5px;'>";
echo "<h3>🎯 Tu sistema actual tiene:</h3>";
echo "<ul>";
if (isset($campos_actuales)) {
    echo "<li>✅ <strong>" . count($campos_actuales) . " campos</strong> en la tabla miembros</li>";
}
if (isset($total)) {
    echo "<li>✅ <strong>$total registros</strong> de miembros</li>";
}
echo "<li>✅ Archivos básicos funcionando</li>";
echo "</ul>";

echo "<h3>🚀 Necesitas agregar:</h3>";
echo "<ul>";
echo "<li>📸 <strong>5 campos nuevos</strong> para datos masónicos</li>";
echo "<li>👥 <strong>Sistema de usuarios</strong> con roles</li>";
echo "<li>📁 <strong>Gestión de archivos</strong> (fotos, documentos)</li>";
echo "<li>🔧 <strong>Configuración flexible</strong> de campos</li>";
echo "<li>🎨 <strong>Interfaz mejorada</strong> con nuevas funcionalidades</li>";
echo "</ul>";
echo "</div>";

echo "<p><strong>📅 Diagnóstico completado: " . date('Y-m-d H:i:s') . "</strong></p>";
echo "<p><a href='test.php' style='color: blue;'>← Volver al diagnóstico básico</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
h2 { color: #666; margin-top: 30px; }
table { width: 100%; margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
pre { background: #f8f8f8; padding: 10px; border-radius: 4px; overflow-x: auto; }
.success { color: #4CAF50; }
.error { color: #f44336; }
.warning { color: #ff9800; }
</style>