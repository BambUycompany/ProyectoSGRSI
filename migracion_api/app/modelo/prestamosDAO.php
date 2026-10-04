<?php
// PrestamosDAO.php

class PrestamosDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista los préstamos asociados a la cédula del usuario (solicitante).
     */
    public function listarPrestamos(string $cedula): array {
        $sql = "SELECT * FROM PRESTAMO WHERE cedula_solicitante = :cedula ORDER BY fecha_prestamo DESC";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Lista únicamente los portátiles que están disponibles para ser prestados.
     */
    public function listarPortatilesDisponibles(): array {
        $sql = "SELECT * FROM PORTATIL WHERE estado = 'disponible'";
        $consulta = $this->conexion->query($sql);
        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Registra un nuevo préstamo y actualiza el estado del portátil.
     * Utiliza una transacción para asegurar que ambas operaciones ocurran juntas.
     */
    public function registrarPrestamo(array $datos): bool {
        try {
            $this->conexion->beginTransaction();

            // 1. Verificar si el portátil sigue disponible (prevención de concurrencia)
            $check = $this->conexion->prepare("SELECT estado FROM PORTATIL WHERE id = :id FOR UPDATE");
            $check->execute(["id" => $datos["portatilId"]]);
            $estado = $check->fetchColumn();

            if ($estado !== 'disponible') {
                $this->conexion->rollBack();
                return false;
            }

            // 2. Insertar el préstamo
            $sqlInsert = "INSERT INTO PRESTAMO (portatil_id, fecha_devolucion, ci_alumno, clase, correo_alumno, telefono_alumno, cedula_solicitante, estado) 
                          VALUES (:portatilId, :fechaDev, :ciAlumno, :clase, :correoAlumno, :telefonoAlumno, :cedula, 'activo')";
            $consultaInsert = $this->conexion->prepare($sqlInsert);
            $consultaInsert->execute([
                "portatilId" => $datos["portatilId"],
                "fechaDev" => $datos["fechaDev"],
                "ciAlumno" => $datos["ciAlumno"],
                "clase" => $datos["clase"],
                "correoAlumno" => $datos["correoAlumno"],
                "telefonoAlumno" => $datos["telefonoAlumno"],
                "cedula" => $datos["cedula"]
            ]);

            // 3. Actualizar el estado del portátil a 'prestado'
            $sqlUpdate = $this->conexion->prepare("UPDATE PORTATIL SET estado = 'prestado' WHERE id = :id");
            $sqlUpdate->execute(["id" => $datos["portatilId"]]);

            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            $this->conexion->rollBack();
            return false;
        }
    }

    /**
     * Finaliza un préstamo activo y vuelve a poner el portátil como disponible.
     */
    public function finalizarPrestamo(int $prestamoId): bool {
        try {
            $this->conexion->beginTransaction();

            // 1. Obtener el ID del portátil asociado a este préstamo
            $sqlGetPortatil = "SELECT portatil_id FROM PRESTAMO WHERE id = :id AND estado = 'activo'";
            $consultaGet = $this->conexion->prepare($sqlGetPortatil);
            $consultaGet->execute(["id" => $prestamoId]);
            $portatilId = $consultaGet->fetchColumn();

            if (!$portatilId) {
                $this->conexion->rollBack();
                return false;
            }

            // 2. Marcar el préstamo como finalizado
            $sqlUpdatePrestamo = $this->conexion->prepare("UPDATE PRESTAMO SET estado = 'finalizado', fecha_entrega = CURRENT_DATE WHERE id = :id");
            $sqlUpdatePrestamo->execute(["id" => $prestamoId]);

            // 3. Liberar el portátil
            $sqlUpdatePortatil = $this->conexion->prepare("UPDATE PORTATIL SET estado = 'disponible' WHERE id = :id");
            $sqlUpdatePortatil->execute(["id" => $portatilId]);

            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            $this->conexion->rollBack();
            return false;
        }
    }
}
?>