<?php
// Iniciar sesión
session_start();

// Si el usuario no viene del procesamiento del formulario, redirigir al inicio
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'process.php') === false) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Exitoso</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="success-container">
        <div class="success-header">
            <h1>¡Registro Completado!</h1>
        </div>
        
        <div class="success-content">
            <div class="icon-success">✓</div>
            
            <p>Los datos han sido registrados correctamente en nuestro sistema.</p>
            
            <p>Gracias por completar el formulario de registro.</p>
            
            <a href="index.php" class="btn">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>