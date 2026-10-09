
<?php

class PrestamoDAO {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
        $this->conexion->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function listarPortatilesDisponibles(): array {

        $sql = "SELECT ID, Modelo
                FROM PORTATIL
                WHERE Estado = 'disponible'
                ORDER BY Modelo, ID";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarPrestamo(array $datos): bool {

        try {
            $this->conexion->beginTransaction();

            $sqlVerificar = "SELECT Estado
                            FROM PORTATIL
                            WHERE ID = :portatilId
                            FOR UPDATE";

            $consultaVerificar = $this->conexion->prepare($sqlVerificar);

            $consultaVerificar->execute([
                "portatilId" => $datos["portatilId"]
            ]);

            $portatil = $consultaVerificar->fetch(PDO::FETCH_ASSOC);

            if (
                $portatil === false ||
                $portatil["Estado"] !== "disponible"
            ) {
                $this->conexion->rollBack();
                return false;
            }

            $sqlPrestamo = "INSERT INTO PRESTAMO (
                                FechaPrestamo,
                                FechaDev,
                                Estado,
                                CIAlumno,
                                Clase,
                                CorreoAlumno,
                                TelefonoAlumno,
                                SolicitanteCedula,
                                PortatilID
                            )
                            VALUES (
                                CURDATE(),
                                CURDATE(),
                                'activo',
                                :ciAlumno,
                                :clase,
                                :correoAlumno,
                                :telefonoAlumno,
                                :cedula,
                                :portatilId
                            )";

            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);

            $consultaPrestamo->execute([
                "ciAlumno" => $datos["ciAlumno"],
                "clase" => $datos["clase"],
                "correoAlumno" => $datos["correoAlumno"],
                "telefonoAlumno" => $datos["telefonoAlumno"],
                "cedula" => $datos["cedula"],
                "portatilId" => $datos["portatilId"]
            ]);

            $sqlActualizar = "UPDATE PORTATIL
                              SET Estado = 'en_prestamo'
                              WHERE ID = :portatilId
                              AND Estado = 'disponible'";

            $consultaActualizar = $this->conexion->prepare($sqlActualizar);

            $consultaActualizar->execute([
                "portatilId" => $datos["portatilId"]
            ]);

            if ($consultaActualizar->rowCount() !== 1) {
                throw new RuntimeException(
                    "No se pudo actualizar el estado del portátil."
                );
            }

            $this->conexion->commit();

            return true;

        } catch (Throwable $error) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log(
                "Error al registrar préstamo: " . $error->getMessage()
            );

            return false;
        }
    }

    public function listarPrestamos(string $cedula): array {

        $sql = "SELECT
                    p.ID,
                    p.FechaPrestamo,
                    p.FechaDev,
                    p.Estado,
                    p.CIAlumno,
                    p.Clase,
                    p.CorreoAlumno,
                    p.TelefonoAlumno,
                    p.SolicitanteCedula,
                    p.PortatilID,
                    pt.Modelo AS PortatilModelo
                FROM PRESTAMO p
                INNER JOIN PORTATIL pt
                    ON pt.ID = p.PortatilID
                WHERE p.SolicitanteCedula = :cedula
                ORDER BY
                    CASE WHEN p.Estado = 'activo' THEN 0 ELSE 1 END,
                    p.FechaPrestamo DESC,
                    p.ID DESC";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodosPrestamos(): array {

        $sql = "SELECT
                    p.ID,
                    p.FechaPrestamo,
                    p.FechaDev,
                    p.Estado,
                    p.CIAlumno,
                    p.Clase,
                    p.CorreoAlumno,
                    p.TelefonoAlumno,
                    p.SolicitanteCedula,
                    p.PortatilID,
                    pt.Modelo AS PortatilModelo,
                    u.Nombre AS DocenteNombre,
                    u.Apellido AS DocenteApellido
                FROM PRESTAMO p
                INNER JOIN PORTATIL pt
                    ON pt.ID = p.PortatilID
                INNER JOIN usuario u
                    ON u.cedula = p.SolicitanteCedula
                ORDER BY
                    CASE WHEN p.Estado = 'activo' THEN 0 ELSE 1 END,
                    p.FechaPrestamo DESC,
                    p.ID DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPrestamoPorId(int $prestamoId): ?array {

        $sql = "SELECT *
                FROM PRESTAMO
                WHERE ID = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "id" => $prestamoId
        ]);

        $prestamo = $consulta->fetch(PDO::FETCH_ASSOC);

        return $prestamo === false ? null : $prestamo;
    }

    public function finalizarPrestamo(int $prestamoId): bool {

        try {
            $this->conexion->beginTransaction();

            $sqlBuscar = "SELECT PortatilID, Estado
                          FROM PRESTAMO
                          WHERE ID = :id
                          FOR UPDATE";

            $consultaBuscar = $this->conexion->prepare($sqlBuscar);

            $consultaBuscar->execute([
                "id" => $prestamoId
            ]);

            $prestamo = $consultaBuscar->fetch(PDO::FETCH_ASSOC);

            if (
                $prestamo === false ||
                $prestamo["Estado"] !== "activo"
            ) {
                $this->conexion->rollBack();
                return false;
            }

            $sqlFinalizar = "UPDATE PRESTAMO
                            SET Estado = 'finalizado'
                            WHERE ID = :id
                            AND Estado = 'activo'";

            $consultaFinalizar = $this->conexion->prepare($sqlFinalizar);

            $consultaFinalizar->execute([
                "id" => $prestamoId
            ]);

            if ($consultaFinalizar->rowCount() !== 1) {
                throw new RuntimeException(
                    "No se pudo finalizar el préstamo."
                );
            }

            $sqlPortatil = "UPDATE PORTATIL
                            SET Estado = 'disponible'
                            WHERE ID = :portatilId
                            AND Estado = 'en_prestamo'";

            $consultaPortatil = $this->conexion->prepare($sqlPortatil);

            $consultaPortatil->execute([
                "portatilId" => $prestamo["PortatilID"]
            ]);

            if ($consultaPortatil->rowCount() !== 1) {
                throw new RuntimeException(
                    "No se pudo liberar el portátil."
                );
            }

            $this->conexion->commit();

            return true;

        } catch (Throwable $error) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log(
                "Error al finalizar préstamo: " . $error->getMessage()
            );

            return false;
        }
    }

    public function modificarDatosAlumno(
        int $prestamoId,
        string $ciAlumno,
        string $clase,
        string $correoAlumno,
        string $telefonoAlumno
    ): bool {

        $sql = "UPDATE PRESTAMO
                SET
                    CIAlumno = :ci,
                    Clase = :clase,
                    CorreoAlumno = :correo,
                    TelefonoAlumno = :telefono
                WHERE ID = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            "ci" => $ciAlumno,
            "clase" => $clase,
            "correo" => $correoAlumno,
            "telefono" => $telefonoAlumno,
            "id" => $prestamoId
        ]);
    }

    public function eliminarPrestamo(int $prestamoId): bool {

        $sql = "DELETE FROM PRESTAMO
                WHERE ID = :id
                AND Estado = 'finalizado'";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "id" => $prestamoId
        ]);

        return $consulta->rowCount() > 0;
    }
}
