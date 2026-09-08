<?php
class UsuarioDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function buscarUsuario(string $cedula): ?array {
        $sql = "
            SELECT
                u.cedula,
                u.claveHash,
                u.activo,

                CASE WHEN a.cedula IS NOT NULL THEN TRUE ELSE FALSE END AS administrador,
                CASE WHEN s.cedula IS NOT NULL THEN TRUE ELSE FALSE END AS solicitante,
                CASE WHEN so.cedula IS NOT NULL THEN TRUE ELSE FALSE END AS soporte

            FROM USUARIO AS u
            LEFT JOIN ADMINISTRADOR AS a ON a.cedula = u.cedula
            LEFT JOIN SOLICITANTE AS s ON s.cedula = u.cedula
            LEFT JOIN SOPORTE AS so ON so.cedula = u.cedula
            WHERE u.cedula = :cedula
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuario === false) {
            return null;
        }

        return [
            "cedula" => $usuario["cedula"],
            "claveHash" => $usuario["claveHash"],
            "activo" => (bool)$usuario["activo"],
            "administrador" => (bool)$usuario["administrador"],
            "soporte" => (bool)$usuario["soporte"],
            "solicitante" => (bool)$usuario["solicitante"]
        ];
    }
}