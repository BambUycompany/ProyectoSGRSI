
<?php

class UsuarioDAO {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;

        $this->conexion->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function buscarUsuario(string $cedula): ?array {

        $sql = "SELECT
                    u.cedula,
                    u.nombre,
                    u.apellido,
                    u.claveHash,
                    u.activo,
                    CASE WHEN a.cedula IS NOT NULL THEN 1 ELSE 0 END AS administrador,
                    CASE WHEN so.cedula IS NOT NULL THEN 1 ELSE 0 END AS soporte,
                    CASE WHEN s.cedula IS NOT NULL THEN 1 ELSE 0 END AS solicitante
                FROM usuario u
                LEFT JOIN administrador a ON a.cedula = u.cedula
                LEFT JOIN soporte so ON so.cedula = u.cedula
                LEFT JOIN solicitante s ON s.cedula = u.cedula
                WHERE u.cedula = :cedula";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula
        ]);

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
            "solicitante" => (bool) $usuario["solicitante"]
        ];
    }

    public function listarUsuarios(): array {

        $sql = "SELECT
                    u.cedula,
                    u.nombre,
                    u.apellido,
                    u.activo,
                    CASE WHEN a.cedula IS NOT NULL THEN 1 ELSE 0 END AS administrador,
                    CASE WHEN so.cedula IS NOT NULL THEN 1 ELSE 0 END AS soporte,
                    CASE WHEN s.cedula IS NOT NULL THEN 1 ELSE 0 END AS solicitante
                FROM usuario u
                LEFT JOIN administrador a ON a.cedula = u.cedula
                LEFT JOIN soporte so ON so.cedula = u.cedula
                LEFT JOIN solicitante s ON s.cedula = u.cedula
                ORDER BY u.nombre, u.apellido";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorCedula(string $cedula): ?array {

        $sql = "SELECT
                    u.cedula,
                    u.nombre,
                    u.apellido,
                    u.activo,
                    CASE WHEN a.cedula IS NOT NULL THEN 1 ELSE 0 END AS administrador,
                    CASE WHEN so.cedula IS NOT NULL THEN 1 ELSE 0 END AS soporte,
                    CASE WHEN s.cedula IS NOT NULL THEN 1 ELSE 0 END AS solicitante
                FROM usuario u
                LEFT JOIN administrador a ON a.cedula = u.cedula
                LEFT JOIN soporte so ON so.cedula = u.cedula
                LEFT JOIN solicitante s ON s.cedula = u.cedula
                WHERE u.cedula = :cedula";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula
        ]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        return $usuario === false ? null : $usuario;
    }

    public function crearUsuario(
        string $cedula,
        string $nombre,
        string $apellido,
        string $claveHash,
        array $roles
    ): bool {

        try {
            $this->conexion->beginTransaction();

            $sqlUsuario = "INSERT INTO usuario (
                                cedula,
                                nombre,
                                apellido,
                                claveHash,
                                activo
                           )
                           VALUES (
                                :cedula,
                                :nombre,
                                :apellido,
                                :claveHash,
                                1
                           )";

            $consulta = $this->conexion->prepare($sqlUsuario);

            $consulta->execute([
                "cedula" => $cedula,
                "nombre" => $nombre,
                "apellido" => $apellido,
                "claveHash" => $claveHash
            ]);

            $this->guardarRoles($cedula, $roles);

            $this->conexion->commit();

            return true;

        } catch (Throwable $error) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log(
                "Error al crear usuario: " . $error->getMessage()
            );

            return false;
        }
    }

    public function modificarUsuario(
        string $cedula,
        string $nombre,
        string $apellido,
        ?string $claveHash,
        array $roles
    ): bool {

        try {
            $this->conexion->beginTransaction();

            if ($claveHash !== null) {

                $sqlUsuario = "UPDATE usuario
                               SET nombre = :nombre,
                                   apellido = :apellido,
                                   claveHash = :claveHash
                               WHERE cedula = :cedula";

                $consulta = $this->conexion->prepare($sqlUsuario);

                $consulta->execute([
                    "nombre" => $nombre,
                    "apellido" => $apellido,
                    "claveHash" => $claveHash,
                    "cedula" => $cedula
                ]);

            } else {

                $sqlUsuario = "UPDATE usuario
                               SET nombre = :nombre,
                                   apellido = :apellido
                               WHERE cedula = :cedula";

                $consulta = $this->conexion->prepare($sqlUsuario);

                $consulta->execute([
                    "nombre" => $nombre,
                    "apellido" => $apellido,
                    "cedula" => $cedula
                ]);
            }

            $this->eliminarRoles($cedula);
            $this->guardarRoles($cedula, $roles);

            $this->conexion->commit();

            return true;

        } catch (Throwable $error) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log(
                "Error al modificar usuario: " . $error->getMessage()
            );

            return false;
        }
    }

    private function guardarRoles(
        string $cedula,
        array $roles
    ): void {

        $tablasPermitidas = [
            "administrador",
            "soporte",
            "solicitante"
        ];

        foreach (array_unique($roles) as $rol) {

            if (!in_array($rol, $tablasPermitidas, true)) {
                throw new InvalidArgumentException(
                    "Rol inválido."
                );
            }

            $sql = "INSERT INTO {$rol} (cedula)
                    VALUES (:cedula)";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "cedula" => $cedula
            ]);
        }
    }

    private function eliminarRoles(string $cedula): void {

        $tablas = [
            "administrador",
            "soporte",
            "solicitante"
        ];

        foreach ($tablas as $tabla) {

            $sql = "DELETE FROM {$tabla}
                    WHERE cedula = :cedula";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "cedula" => $cedula
            ]);
        }
    }

    public function desactivarUsuario(string $cedula): bool {

        $sql = "UPDATE usuario
                SET activo = 0
                WHERE cedula = :cedula
                AND activo = 1";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula
        ]);

        return $consulta->rowCount() > 0;
    }

    public function reactivarUsuario(string $cedula): bool {

        $sql = "UPDATE usuario
                SET activo = 1
                WHERE cedula = :cedula
                AND activo = 0";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula
        ]);

        return $consulta->rowCount() > 0;
    }
}
