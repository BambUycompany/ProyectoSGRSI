<?php
function requerirSesion(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["cedula"])) {
        header("Location: ../vista/login.php?error=sinsesion");
        exit;
    }
}

function requerirRol(string $rol): void {
    requerirSesion();
    if (empty($_SESSION[$rol])) {
        header("Location: ../vista/login.php?error=noautorizado");
        exit;
    }
}

?>