<?php

/**
 * Clase que simula una recuperación de credenciales correspondientes a la base de datos.
 */
class ConsultaUsuario {
    /**
     * Simula la recuperación de un usuario desde una base de datos.
     *
     * Más adelante, el contenido de esta función será reemplazado
     * por una consulta mediante PDO.
     */
    public function buscarUsuario(string $cedula): ?Usuario {
        $usuariosRegistrados = [
            "1111" => [
                "cedula" => "1111",
                "claveHash" => password_hash("clave1234567", PASSWORD_DEFAULT),
                "activo" => true,
                "administrador" => true,
                "soporte" => false,
                "solicitante" => false,
            ],
            "2222" => [
                "cedula" => "2222",
                "claveHash" => password_hash("soporte123", PASSWORD_DEFAULT),
                "activo" => true,
                "administrador" => false,
                "soporte" => true,
                "solicitante" => false,
            ],
            "3333" => [
                "cedula" => "3333",
                "claveHash" => password_hash("user123456", PASSWORD_DEFAULT),
                "activo" => true,
                "administrador" => false,
                "soporte" => false,
                "solicitante" => true,
            ],
        ];

        if (!isset($usuariosRegistrados[$cedula])) {
            return null;
        }

        $datos = $usuariosRegistrados[$cedula];

        return new Usuario (
            $datos["cedula"],
            $datos["claveHash"],
            $datos["activo"],
            $datos["administrador"],
            $datos["solicitante"],
            $datos["soporte"],
        );
    }
}

?>