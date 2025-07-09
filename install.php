<?php
// Este script ayudará a configurar la base de datos

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $host = $_POST['host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? 'sistema_registro';
    $username = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    
    try {
        // Intentar conectar a MySQL/MariaDB sin seleccionar la base de datos
        $conn = new PDO("mysql:host=$host", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Leer el archivo SQL
        $sql_content = file_get_contents('config/install.sql');
        
        // Reemplazar el nombre de la base de datos en el SQL si es diferente al predeterminado
        if ($db_name !== 'sistema_registro') {
            $sql_content = str_replace('sistema_registro', $db_name, $sql_content);
        }
        
        // Ejecutar las consultas SQL
        $conn->exec($sql_content);
        
        // Crear/actualizar el archivo de configuración
        $config_content = '<?php
// Configuración de la base de datos
class Database {
    // Parámetros de conexión
    private $host = "' . $host . '";
    private $db_name = "' . $db_name . '";
    private $username = "' . $username . '";
    private $password = "' . $password . '";
    public $conn;
    
    // Método para obtener la conexión
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>';
        
        // Guardar el archivo de configuración
        file_put_contents('config/database.php', $config_content);
        
        $success = "¡Instalación completada con éxito! La base de datos ha sido configurada y el sistema está listo para usar.";
        
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación del Sistema</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Instalación del Sistema de Registro</h1>
            <p>Complete la información para configurar la base de datos</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div style="background-color: #fee2e2; border: 1px solid #ef4444; padding: 15px; border-radius: 5px; margin-bottom: 20px; color: #b91c1c;">
                    <strong>Error:</strong> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div style="background-color: #dcfce7; border: 1px solid #10b981; padding: 15px; border-radius: 5px; margin-bottom: 20px; color: #047857;">
                    <strong>¡Éxito!</strong> <?php echo $success; ?>
                    <p style="margin-top: 10px;">
                        <a href="index.php" class="btn" style="display: inline-block; margin-right: 10px;">Ir al Formulario</a>
                        <a href="login.php" class="btn btn-submit" style="display: inline-block;">Iniciar Sesión</a>
                    </p>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="section">
                        <h2 class="section-title">Configuración de la Base de Datos</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="host">Servidor de Base de Datos:</label>
                                <input type="text" id="host" name="host" value="localhost" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="db_name">Nombre de la Base de Datos:</label>
                                <input type="text" id="db_name" name="db_name" value="sistema_registro" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Usuario de la Base de Datos:</label>
                                <input type="text" id="username" name="username" value="root" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Contraseña:</label>
                                <input type="password" id="password" name="password">
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-container">
                        <button type="submit" class="btn btn-submit">Instalar Sistema</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>