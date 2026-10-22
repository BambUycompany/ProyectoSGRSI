<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenJWT
{
    private static function obtenerClave(): string
    {
        $clave = $_ENV["JWT_SECRET"] ?? "";

        if (strlen($clave) < 32) {
            throw new RuntimeException(
                "La clave JWT no está configurada."
            );
        }

        return $clave;
    }

    public static function generar(
        string $cedula,
        array $roles
    ): string {
        $ahora = time();
        $expiracion = (int) ($_ENV["JWT_EXPIRACION"] ?? 3600);

        return JWT::encode([
            "iss" => "SGRSI",
            "sub" => $cedula,
            "roles" => $roles,
            "iat" => $ahora,
            "nbf" => $ahora,
            "exp" => $ahora + $expiracion
        ], self::obtenerClave(), "HS256");
    }

    public static function validar(string $token): ?array
    {
        try {
            $datos = JWT::decode(
                $token,
                new Key(self::obtenerClave(), "HS256")
            );

            if (
                ($datos->iss ?? "") !== "SGRSI" ||
                empty($datos->sub) ||
                !isset($datos->roles) ||
                !is_array($datos->roles)
            ) {
                return null;
            }

            return (array) $datos;

        } catch (Exception $error) {
            return null;
        }
    }
}