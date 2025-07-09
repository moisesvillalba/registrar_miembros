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

try {
    // Primero, obtener información del archivo (si existe)
    $sql_select = "SELECT documentos FROM miembros WHERE id = :id";
    $stmt_select = $db->prepare($sql_select);
    $stmt_select->bindParam(':id', $id);
    $stmt_select->execute();
    
    if ($stmt_select->rowCount() > 0) {
        $row = $stmt_select->fetch(PDO::FETCH_ASSOC);
        $documentos = $row['documentos'];
        
        // Preparar consulta para eliminar el registro
        $sql_delete = "DELETE FROM miembros WHERE id = :id";
        $stmt_delete = $db->prepare($sql_delete);
        $stmt_delete->bindParam(':id', $id);
        
        // Ejecutar la consulta
        if ($stmt_delete->execute()) {
            // Eliminar el archivo físico si existe
            if (!empty($documentos) && file_exists($documentos)) {
                unlink($documentos);
            }
            
            // Redirigir con mensaje de éxito
            header("Location: list.php?token=demo123&msg=delete_success");
            exit();
        } else {
            // Error en la eliminación
            header("Location: list.php?token=demo123&msg=delete_error");
            exit();
        }
    } else {
        // No se encontró el registro
        header("Location: list.php?token=demo123&msg=not_found");
        exit();
    }
} catch (PDOException $e) {
    // Error en la base de datos
    header("Location: list.php?token=demo123&msg=db_error");
    exit();
}
?>