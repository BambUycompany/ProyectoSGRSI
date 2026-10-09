<?php

class UsuarioDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }
    public function buscarUsuario(string $cedula): ?array {
        $sql = "SELECT
                    u.cedula, u.nombre, u.apellido, u.claveHash, u.activo,
                    CASE WHEN a.cedula IS NOT NULL THEN TRUE ELSE FALSE END AS administrador,
                    CASE WHEN so.cedula IS NOT NULL THEN TRUE ELSE FALSE END AS soporte,
                    CASE WHEN s.cedula IS NOT NULL THEN TRUE ELSE FALSE END AS solicitante
                FROM usuario AS u
                LEFT JOIN administrador AS a ON a.cedula = u.cedula
                LEFT JOIN soporte AS so ON so.cedula = u.cedula
                LEFT JOIN solicitante AS s ON s.cedula = u.cedula
                WHERE u.cedula = :cedula";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuario === false) {
            return null;
        }

        return [
            "cedula" => $usuario["cedula"],
            "nombre" => $usuario["nombre"],
            "apellido" => $usuario["apellido"],
            "claveHash" => $usuario["claveHash"],
            "activo" => (bool) $usuario["activo"],
            "administrador" => (bool) $usuario["administrador"],
            "soporte" => (bool) $usuario["soporte"],
            "solicitante" => (bool) $usuario["solicitante"],
        ];
    }

    public function listarUsuarios(): array {
        $sql = "SELECT 
                    u.cedula, u.nombre, u.apellido,
                    CASE WHEN a.cedula IS NOT NULL THEN 1 ELSE 0 END AS administrador,
                    CASE WHEN so.cedula IS NOT NULL THEN 1 ELSE 0 END AS soporte,
                    CASE WHEN s.cedula IS NOT NULL THEN 1 ELSE 0 END AS solicitante
                FROM usuario u
                LEFT JOIN administrador a ON a.cedula = u.cedula
                LEFT JOIN soporte so ON so.cedula = u.cedula
                LEFT JOIN solicitante s ON s.cedula = u.cedula
                ORDER BY u.nombre";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerPorCedula(string $cedula): ?array {
        $sql = "SELECT 
                    u.cedula, u.nombre, u.apellido,
                    CASE WHEN a.cedula IS NOT NULL THEN 1 ELSE 0 END AS administrador,
                    CASE WHEN so.cedula IS NOT NULL THEN 1 ELSE 0 END AS soporte,
                    CASE WHEN s.cedula IS NOT NULL THEN 1 ELSE 0 END AS solicitante
                FROM usuario u
                LEFT JOIN administrador a ON a.cedula = u.cedula
                LEFT JOIN soporte so ON so.cedula = u.cedula
                LEFT JOIN solicitante s ON s.cedula = u.cedula
                WHERE u.cedula = :cedula";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    public function crearUsuario(string $cedula, string $nombre, string $apellido, string $claveHash, string $rol): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "INSERT INTO usuario (cedula, nombre, apellido, claveHash) VALUES (:cedula, :nombre, :apellido, :claveHash)";
            $this->conexion->prepare($sql)->execute([
                "cedula" => $cedula, "nombre" => $nombre, "apellido" => $apellido, "claveHash" => $claveHash
            ]);

            $tablaRol = match ($rol) {
                "administrador" => "administrador",
                "soporte" => "soporte",
                "solicitante" => "solicitante",
                default => null,
            };

            if ($tablaRol === null) {
                $this->conexion->rollBack();
                return false;
            }

            $this->conexion->prepare("INSERT INTO $tablaRol (cedula) VALUES (:cedula)")->execute(["cedula" => $cedula]);

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function modificarUsuario(string $cedula, string $nombre, string $apellido, ?string $claveHash, string $rol): bool {
        try {
            $this->conexion->beginTransaction();

            if ($claveHash !== null) {
                $sql = "UPDATE usuario SET nombre = :nombre, apellido = :apellido, claveHash = :claveHash WHERE cedula = :cedula";
                $this->conexion->prepare($sql)->execute([
                    "nombre" => $nombre, "apellido" => $apellido, "claveHash" => $claveHash, "cedula" => $cedula
                ]);
            } else {
                $sql = "UPDATE usuario SET nombre = :nombre, apellido = :apellido WHERE cedula = :cedula";
                $this->conexion->prepare($sql)->execute(["nombre" => $nombre, "apellido" => $apellido, "cedula" => $cedula]);
            }

            $this->conexion->prepare("DELETE FROM administrador WHERE cedula = :cedula")->execute(["cedula" => $cedula]);
            $this->conexion->prepare("DELETE FROM soporte WHERE cedula = :cedula")->execute(["cedula" => $cedula]);
            $this->conexion->prepare("DELETE FROM solicitante WHERE cedula = :cedula")->execute(["cedula" => $cedula]);

            $tablaRol = match ($rol) {
                "administrador" => "administrador",
                "soporte" => "soporte",
                "solicitante" => "solicitante",
                default => null,
            };

            if ($tablaRol === null) {
                $this->conexion->rollBack();
                return false;
            }

            $this->conexion->prepare("INSERT INTO $tablaRol (cedula) VALUES (:cedula)")->execute(["cedula" => $cedula]);

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function desactivarUsuario(string $cedula): bool {
        $sql = "UPDATE usuario SET activo = 0 WHERE cedula = :cedula";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);
        return $consulta->rowCount() > 0;
    }
}

?>