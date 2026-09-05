<?php
//comentar
function requerirSesion(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["cedula"])) {
        header("Location: login.php?error=sinSesion");
        exit;
    }
}

function requerirRol(string|array $roles): void {
    requerirSesion();

    $rolesRequeridos = is_array($roles) ? $roles : [$roles];

    foreach ($rolesRequeridos as $rol) {
        if (!empty($_SESSION[$rol])) {
                return; 
            }
    }

    header("Location: login.php?error=noAutorizado");
    exit;

}
?> 