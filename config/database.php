<?php

/**
 * config/database.php
 * Configuración optimizada para Byethost hosting
 * Sistema de Registro Masónico
 */

class Database
{
    // Configuración para PRODUCCIÓN (Byethost)
    public $host;
    public $db_name;
    public $username;
    public $password;
    public $conn;
    public $connection_error;
    public $last_error;

    public function __construct()
    {
        // Credenciales para Byethost
        $this->host = "sql111.byethost3.com";
        $this->db_name = "b3_38781053_sistema_registro";
        $this->username = "b3_38781053";
        $this->password = "s8Fg\$mHNh#CL#kn";
        $this->conn = null;
        $this->connection_error = null;
        $this->last_error = null;
    }

    /**
     * Obtener conexión PDO optimizada
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            // Opciones optimizadas para Byethost
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::ATTR_PERSISTENT => false, // Evitar problemas en hosting compartido
                PDO::ATTR_TIMEOUT => 30
            );

            // DSN completo con opciones de charset
            $dsn = "mysql:host=" . $this->host .
                ";dbname=" . $this->db_name .
                ";charset=utf8mb4" .
                ";port=3306";

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

            // Configuraciones adicionales después de la conexión
            $this->conn->exec("SET time_zone = '+00:00'");
            $this->conn->exec("SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
        } catch (PDOException $exception) {
            $this->connection_error = $exception->getMessage();
            $this->last_error = $exception;

            // Log del error (sin exponer credenciales)
            error_log("Database connection error: " . $exception->getMessage());

            return null;
        }

        return $this->conn;
    }

    /**
     * Verificar si la conexión está activa
     */
    public function isConnected()
    {
        try {
            if ($this->conn === null) {
                return false;
            }

            $this->conn->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtener último error
     */
    public function getLastError()
    {
        return $this->last_error;
    }

    /**
     * Cerrar conexión
     */
    public function closeConnection()
    {
        $this->conn = null;
    }

    /**
     * Test de conexión rápido
     */
    public function testConnection()
    {
        $conn = $this->getConnection();

        if ($conn === null) {
            return [
                'success' => false,
                'message' => 'No se pudo conectar a la base de datos',
                'error' => $this->connection_error
            ];
        }

        try {
            // Test básico
            $stmt = $conn->query("SELECT 1 as test");
            $result = $stmt->fetch();

            if ($result && $result['test'] == 1) {
                return [
                    'success' => true,
                    'message' => 'Conexión exitosa',
                    'server_info' => $conn->getAttribute(PDO::ATTR_SERVER_VERSION)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Test de consulta falló'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error en test de conexión: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crear tabla miembros si no existe
     */
    public function createMembersTableIfNotExists()
    {
        $conn = $this->getConnection();

        if ($conn === null) {
            return false;
        }

        try {
            $createTableSQL = "
            CREATE TABLE IF NOT EXISTS miembros (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ci VARCHAR(20) NOT NULL,
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
                UNIQUE KEY unique_ci (ci),
                INDEX idx_logia (logia_actual),
                INDEX idx_fecha_registro (fecha_registro),
                INDEX idx_nombre (nombre, apellido)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";

            $conn->exec($createTableSQL);
            return true;
        } catch (PDOException $e) {
            $this->last_error = $e;
            error_log("Error creating table: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si tabla miembros existe
     */
    public function tableExists($tableName = 'miembros')
    {
        $conn = $this->getConnection();

        if ($conn === null) {
            return false;
        }

        try {
            $stmt = $conn->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$tableName]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtener estadísticas de la tabla miembros
     */
    public function getMembersStats()
    {
        $conn = $this->getConnection();

        if ($conn === null) {
            return null;
        }

        try {
            $stats = [];

            // Total de miembros
            $stmt = $conn->query("SELECT COUNT(*) as total FROM miembros");
            $stats['total'] = $stmt->fetchColumn();

            // Miembros por logia
            $stmt = $conn->query("
                SELECT logia_actual, COUNT(*) as count 
                FROM miembros 
                WHERE logia_actual IS NOT NULL 
                GROUP BY logia_actual 
                ORDER BY count DESC 
                LIMIT 10
            ");
            $stats['por_logia'] = $stmt->fetchAll();

            // Registros recientes
            $stmt = $conn->query("
                SELECT COUNT(*) as recientes 
                FROM miembros 
                WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
            $stats['recientes'] = $stmt->fetchColumn();

            return $stats;
        } catch (PDOException $e) {
            error_log("Error getting stats: " . $e->getMessage());
            return null;
        }
    }
}

// Test rápido si se accede directamente
if (basename($_SERVER['PHP_SELF']) === 'database.php') {
    header('Content-Type: text/html; charset=utf-8');

    echo "<!DOCTYPE html>\n";
    echo "<html><head><meta charset='UTF-8'><title>Database Test</title></head><body>\n";
    echo "<h2>Test Rápido de Database</h2>\n";

    $db = new Database();
    $test = $db->testConnection();

    if ($test['success']) {
        echo "<p style='color: green;'>✅ " . $test['message'] . "</p>\n";
        if (isset($test['server_info'])) {
            echo "<p>Server: " . $test['server_info'] . "</p>\n";
        }
    } else {
        echo "<p style='color: red;'>❌ " . $test['message'] . "</p>\n";
        if (isset($test['error'])) {
            echo "<p>Error: " . htmlspecialchars($test['error']) . "</p>\n";
        }
    }

    echo "<p><a href='../test_database.php'>Test Completo</a></p>\n";
    echo "</body></html>\n";
}
