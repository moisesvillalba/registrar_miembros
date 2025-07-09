<?php
/**
 * test_database.php
 * Script para probar la conectividad y estructura de la base de datos
 * Optimizado para Byethost hosting
 */

// Configuración de errores para diagnóstico
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>\n";
echo "<html lang='es'>\n";
echo "<head>\n";
echo "<meta charset='UTF-8'>\n";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "<title>Test de Base de Datos - Sistema Masónico</title>\n";
echo "<style>\n";
echo "body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 20px; background: #f5f5f5; }\n";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }\n";
echo ".success { color: #10b981; background: #d1fae5; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #10b981; }\n";
echo ".error { color: #ef4444; background: #fee2e2; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ef4444; }\n";
echo ".info { color: #3b82f6; background: #dbeafe; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #3b82f6; }\n";
echo ".warning { color: #f59e0b; background: #fef3c7; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #f59e0b; }\n";
echo ".test-section { margin: 20px 0; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; }\n";
echo ".code-block { background: #1f2937; color: #f9fafb; padding: 15px; border-radius: 6px; font-family: 'Courier New', monospace; font-size: 14px; overflow-x: auto; }\n";
echo "h1, h2, h3 { color: #1f2937; }\n";
echo "h1 { text-align: center; border-bottom: 3px solid #8B0000; padding-bottom: 10px; }\n";
echo ".btn { display: inline-block; padding: 12px 24px; background: #8B0000; color: white; text-decoration: none; border-radius: 6px; margin: 10px 5px; }\n";
echo ".btn:hover { background: #5D0000; }\n";
echo "</style>\n";
echo "</head>\n";
echo "<body>\n";
echo "<div class='container'>\n";

echo "<h1>🔍 Test de Base de Datos - Sistema Masónico</h1>\n";

echo "<div class='info'>\n";
echo "<h3>📋 Información del Sistema</h3>\n";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_NAME'] . "</p>\n";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>\n";
echo "<p><strong>Fecha/Hora:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
echo "<p><strong>User Agent:</strong> " . $_SERVER['HTTP_USER_AGENT'] . "</p>\n";
echo "</div>\n";

// Paso 1: Verificar archivo de configuración
echo "<div class='test-section'>\n";
echo "<h3>🔧 Paso 1: Verificación de Archivos</h3>\n";

if (file_exists('config/database.php')) {
    echo "<div class='success'>✅ Archivo config/database.php encontrado</div>\n";
    
    try {
        require_once 'config/database.php';
        echo "<div class='success'>✅ Archivo config/database.php cargado correctamente</div>\n";
        
        $database = new Database();
        echo "<div class='success'>✅ Clase Database instanciada correctamente</div>\n";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error al cargar config/database.php: " . htmlspecialchars($e->getMessage()) . "</div>\n";
    }
} else {
    echo "<div class='error'>❌ Archivo config/database.php NO encontrado</div>\n";
    echo "<div class='warning'>⚠️ Creando archivo de configuración básico...</div>\n";
    
    // Crear directorio config si no existe
    if (!is_dir('config')) {
        mkdir('config', 0755, true);
    }
    
    // Crear archivo database.php básico
    $configContent = '<?php
class Database
{
    public $host;
    public $db_name;
    public $username;
    public $password;
    public $conn;
    public $connection_error;

    public function __construct()
    {
        $this->host = "sql111.byethost3.com";
        $this->db_name = "b3_38781053_sistema_registro";
        $this->username = "b3_38781053";
        $this->password = "s8Fg\$mHNh#CL#kn";
        $this->conn = null;
        $this->connection_error = null;
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                )
            );
        } catch (PDOException $exception) {
            $this->connection_error = $exception->getMessage();
            return null;
        }

        return $this->conn;
    }
}';
    
    if (file_put_contents('config/database.php', $configContent)) {
        echo "<div class='success'>✅ Archivo config/database.php creado exitosamente</div>\n";
        require_once 'config/database.php';
        $database = new Database();
    } else {
        echo "<div class='error'>❌ No se pudo crear el archivo config/database.php</div>\n";
    }
}
echo "</div>\n";

// Paso 2: Test de conexión
echo "<div class='test-section'>\n";
echo "<h3>🌐 Paso 2: Test de Conexión a Base de Datos</h3>\n";

if (isset($database)) {
    echo "<div class='info'>\n";
    echo "<p><strong>Host:</strong> " . $database->host . "</p>\n";
    echo "<p><strong>Base de Datos:</strong> " . $database->db_name . "</p>\n";
    echo "<p><strong>Usuario:</strong> " . $database->username . "</p>\n";
    echo "<p><strong>Password:</strong> " . str_repeat('*', strlen($database->password)) . "</p>\n";
    echo "</div>\n";
    
    $conn = $database->getConnection();
    
    if ($conn !== null) {
        echo "<div class='success'>✅ Conexión a base de datos EXITOSA</div>\n";
        
        // Test básico de consulta
        try {
            $stmt = $conn->query("SELECT VERSION() as version");
            $result = $stmt->fetch();
            echo "<div class='success'>✅ MySQL Version: " . $result['version'] . "</div>\n";
            
            $stmt = $conn->query("SELECT DATABASE() as db_name");
            $result = $stmt->fetch();
            echo "<div class='success'>✅ Base de datos activa: " . $result['db_name'] . "</div>\n";
            
        } catch (PDOException $e) {
            echo "<div class='error'>❌ Error en consulta de prueba: " . htmlspecialchars($e->getMessage()) . "</div>\n";
        }
        
    } else {
        echo "<div class='error'>❌ ERROR de conexión a base de datos</div>\n";
        if ($database->connection_error) {
            echo "<div class='error'>Detalles: " . htmlspecialchars($database->connection_error) . "</div>\n";
        }
    }
} else {
    echo "<div class='error'>❌ No se pudo instanciar la clase Database</div>\n";
}
echo "</div>\n";

// Paso 3: Verificar/Crear tabla miembros
if (isset($conn) && $conn !== null) {
    echo "<div class='test-section'>\n";
    echo "<h3>📊 Paso 3: Verificación de Tabla 'miembros'</h3>\n";
    
    try {
        // Verificar si la tabla existe
        $stmt = $conn->query("SHOW TABLES LIKE 'miembros'");
        $tableExists = $stmt->rowCount() > 0;
        
        if ($tableExists) {
            echo "<div class='success'>✅ Tabla 'miembros' encontrada</div>\n";
            
            // Mostrar estructura de la tabla
            $stmt = $conn->query("DESCRIBE miembros");
            $columns = $stmt->fetchAll();
            
            echo "<div class='info'>\n";
            echo "<h4>📋 Estructura actual de la tabla:</h4>\n";
            echo "<div class='code-block'>\n";
            foreach ($columns as $column) {
                echo htmlspecialchars($column['Field']) . " - " . htmlspecialchars($column['Type']) . 
                     ($column['Null'] === 'NO' ? ' NOT NULL' : ' NULL') . 
                     ($column['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . 
                     ($column['Default'] !== null ? ' DEFAULT ' . htmlspecialchars($column['Default']) : '') . "\n";
            }
            echo "</div>\n";
            echo "</div>\n";
            
            // Contar registros
            $stmt = $conn->query("SELECT COUNT(*) as total FROM miembros");
            $result = $stmt->fetch();
            echo "<div class='info'>📈 Total de registros: " . $result['total'] . "</div>\n";
            
        } else {
            echo "<div class='warning'>⚠️ Tabla 'miembros' NO existe. Creando tabla...</div>\n";
            
            $createTableSQL = "
            CREATE TABLE miembros (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ci VARCHAR(20) NOT NULL UNIQUE,
                nombre VARCHAR(100) NOT NULL,
                apellido VARCHAR(100) NOT NULL,
                fecha_nacimiento DATE,
                lugar VARCHAR(100),
                profesion VARCHAR(100),
                direccion TEXT,
                telefono VARCHAR(30),
                ciudad VARCHAR(100),
                barrio VARCHAR(100),
                esposa VARCHAR(100),
                hijos TEXT,
                madre VARCHAR(100),
                padre VARCHAR(100),
                direccion_laboral TEXT,
                empresa VARCHAR(200),
                logia_actual VARCHAR(200),
                grado_masonico VARCHAR(50),
                fecha_iniciacion DATE,
                logia_iniciacion VARCHAR(200),
                grupo_sanguineo VARCHAR(10),
                enfermedades_base TEXT,
                seguro_privado ENUM('Si','No') DEFAULT 'No',
                ips ENUM('Si','No') DEFAULT 'No',
                alergias TEXT,
                numero_emergencia VARCHAR(30),
                contacto_emergencia VARCHAR(100),
                descripcion_otros_trabajos TEXT,
                grados_que_tienen TEXT,
                logia_perfeccion VARCHAR(200),
                descripcion_grado_capitular TEXT,
                fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_ci (ci),
                INDEX idx_logia (logia_actual),
                INDEX idx_fecha_registro (fecha_registro)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            if ($conn->exec($createTableSQL)) {
                echo "<div class='success'>✅ Tabla 'miembros' creada exitosamente</div>\n";
                echo "<div class='info'>📋 Tabla creada con todas las columnas necesarias para el formulario</div>\n";
            } else {
                echo "<div class='error'>❌ Error al crear tabla 'miembros'</div>\n";
            }
        }
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Error verificando tabla: " . htmlspecialchars($e->getMessage()) . "</div>\n";
    }
    echo "</div>\n";
    
    // Paso 4: Test de inserción
    echo "<div class='test-section'>\n";
    echo "<h3>🧪 Paso 4: Test de Inserción de Datos</h3>\n";
    
    try {
        // Verificar si ya existe un registro de prueba
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM miembros WHERE ci = ?");
        $stmt->execute(['999999']);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            echo "<div class='warning'>⚠️ Registro de prueba ya existe. Eliminando...</div>\n";
            $stmt = $conn->prepare("DELETE FROM miembros WHERE ci = ?");
            $stmt->execute(['999999']);
        }
        
        // Insertar registro de prueba
        $insertSQL = "INSERT INTO miembros (
            ci, nombre, apellido, fecha_nacimiento, lugar, profesion, direccion, telefono, 
            ciudad, barrio, direccion_laboral, logia_actual, grado_masonico, fecha_iniciacion,
            grupo_sanguineo, numero_emergencia, contacto_emergencia
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $testData = [
            '999999',
            'Test',
            'Prueba',
            '1980-01-01',
            'Asunción',
            'Desarrollador',
            'Dirección de prueba 123',
            '+595 987 654 321',
            'Asunción',
            'Centro',
            'Oficina de prueba 456',
            'A∴R∴L∴ Nueva Alianza Nº 1',
            'maestro',
            '2020-01-01',
            'O+',
            '+595 987 654 321',
            'Contacto de Emergencia Test'
        ];
        
        $stmt = $conn->prepare($insertSQL);
        
        if ($stmt->execute($testData)) {
            $insertId = $conn->lastInsertId();
            echo "<div class='success'>✅ Inserción de prueba EXITOSA (ID: $insertId)</div>\n";
            
            // Verificar que se insertó correctamente
            $stmt = $conn->prepare("SELECT * FROM miembros WHERE id = ?");
            $stmt->execute([$insertId]);
            $insertedRecord = $stmt->fetch();
            
            if ($insertedRecord) {
                echo "<div class='success'>✅ Verificación de datos insertados: OK</div>\n";
                echo "<div class='info'>\n";
                echo "<h4>📋 Datos insertados:</h4>\n";
                echo "<div class='code-block'>\n";
                echo "ID: " . $insertedRecord['id'] . "\n";
                echo "CI: " . $insertedRecord['ci'] . "\n";
                echo "Nombre: " . $insertedRecord['nombre'] . " " . $insertedRecord['apellido'] . "\n";
                echo "Logia: " . $insertedRecord['logia_actual'] . "\n";
                echo "Fecha Registro: " . $insertedRecord['fecha_registro'] . "\n";
                echo "</div>\n";
                echo "</div>\n";
                
                // Limpiar registro de prueba
                $stmt = $conn->prepare("DELETE FROM miembros WHERE id = ?");
                $stmt->execute([$insertId]);
                echo "<div class='info'>🧹 Registro de prueba eliminado</div>\n";
            }
        } else {
            echo "<div class='error'>❌ Error en inserción de prueba</div>\n";
        }
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Error en test de inserción: " . htmlspecialchars($e->getMessage()) . "</div>\n";
    }
    echo "</div>\n";
}

// Resumen final
echo "<div class='test-section'>\n";
echo "<h3>📋 Resumen Final</h3>\n";

$allTestsPassed = isset($conn) && $conn !== null;

if ($allTestsPassed) {
    echo "<div class='success'>\n";
    echo "<h4>🎉 ¡TODOS LOS TESTS PASARON EXITOSAMENTE!</h4>\n";
    echo "<p>✅ Conexión a base de datos: OK</p>\n";
    echo "<p>✅ Tabla 'miembros': OK</p>\n";
    echo "<p>✅ Inserción de datos: OK</p>\n";
    echo "<p>✅ El sistema está listo para usar</p>\n";
    echo "</div>\n";
} else {
    echo "<div class='error'>\n";
    echo "<h4>❌ HAY PROBLEMAS QUE RESOLVER</h4>\n";
    echo "<p>Revisa los errores anteriores y contacta al soporte técnico si es necesario.</p>\n";
    echo "</div>\n";
}

echo "<div style='text-align: center; margin-top: 30px;'>\n";
echo "<a href='index.php' class='btn'>🔙 Volver al Formulario</a>\n";
echo "<a href='test_database.php' class='btn' onclick='location.reload(); return false;'>🔄 Ejecutar Tests Nuevamente</a>\n";
echo "</div>\n";

echo "</div>\n";

echo "<div style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px; font-size: 12px; color: #6c757d; text-align: center;'>\n";
echo "<p><strong>Sistema de Registro - Gran Logia de Libres y Aceptados Masones del Paraguay</strong></p>\n";
echo "<p>Test ejecutado el " . date('d/m/Y H:i:s') . " | PHP " . phpversion() . "</p>\n";
echo "</div>\n";

echo "</body>\n";
echo "</html>\n";
?>