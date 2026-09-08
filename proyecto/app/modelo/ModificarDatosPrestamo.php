<?php
class ModificarDatosPrestamo {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

   
    public function registrarDevolucion(int $prestamoId): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlBuscar = "SELECT PortatilID, Estado FROM PORTATIL WHERE PortatilID = :id FOR UPDATE";
            $consultaBuscar = $this->conexion->prepare($sqlBuscar);
            $consultaBuscar->execute(["id" => $prestamoId]);
            $prestamo = $consultaBuscar->fetch(PDO::FETCH_ASSOC);

            if ($prestamo === false || $prestamo["Estado"] !== "activo") {
                $this->conexion->rollBack();
                return false;
            }

            $this->conexion->prepare("UPDATE PORTATIL SET Estado = 'devuelto' WHERE ID = :id")
                ->execute(["id" => $prestamoId]);

            $this->conexion->prepare("UPDATE PORTATIL SET Estado = 'disponible' WHERE ID = :portatilId")
                ->execute(["portatilId" => $prestamo["PortatilID"]]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function modificarDatosAlumno(int $prestamoId, string $ciAlumno, string $clase, string $correoAlumno, string $telefonoAlumno): bool {
        $sql = "UPDATE PRESTAMO SET CIAlumno = :ci, Clase = :clase, CorreoAlumno = :correo, TelefonoAlumno = :telefono WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            "ci" => $ciAlumno,
            "clase" => $clase,
            "correo" => $correoAlumno,
            "telefono" => $telefonoAlumno,
            "id" => $prestamoId,
        ]);
    }
}