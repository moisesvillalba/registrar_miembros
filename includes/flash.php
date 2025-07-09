<?php
// Verificar si la sesión ya está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Función para establecer un mensaje flash
function setFlashMessage($tipo, $mensaje) {
    $_SESSION['flash_message'] = [
        'tipo' => $tipo,
        'mensaje' => $mensaje
    ];
}

// Función para obtener y limpiar un mensaje flash
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $mensaje = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $mensaje;
    }
    return null;
}

// Clase Flash como respaldo (opcional)
class Flash {
    // Método estático para establecer mensaje
    public static function set($tipo, $mensaje) {
        setFlashMessage($tipo, $mensaje);
    }

    // Método estático para obtener mensaje
    public static function get() {
        return getFlashMessage();
    }

    // Verificar si hay un mensaje flash
    public static function has() {
        return isset($_SESSION['flash_message']);
    }
}
?>