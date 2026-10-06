<?php

class PrestamoDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

  

    public function crearPortatil(string $modelo): bool
    {
        $sql = "INSERT INTO PORTATIL (Modelo, Estado) VALUES (:modelo, 'disponible')";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute(["modelo" => $modelo]);
    }

    public function modificarPortatil(int $portatilId, string $modelo): bool
    {
        $sql = "UPDATE PORTATIL SET Modelo = :modelo WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute(["modelo" => $modelo, "id" => $portatilId]);
    }

    public function deshabilitarPortatil(int $portatilId): bool
    {
        $sql = "UPDATE PORTATIL SET Estado = 'deshabilitado' WHERE ID = :id AND Estado = 'disponible'";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $portatilId]);
        return $consulta->rowCount() > 0;
    }

    public function habilitarPortatil(int $portatilId): bool
    {
        $sql = "UPDATE PORTATIL SET Estado = 'disponible' WHERE ID = :id AND Estado = 'deshabilitado'";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $portatilId]);
        return $consulta->rowCount() > 0;
    }

    public function eliminarPortatil(int $portatilId): bool
    {
        try {
            $sql = "DELETE FROM PORTATIL WHERE ID = :id AND Estado = 'disponible'";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute(["id" => $portatilId]);
            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function listarTodasPortatiles(): array
    {
        $sql = "SELECT ID, Modelo, Estado FROM PORTATIL ORDER BY Modelo";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPortatilesDisponibles(): array
    {
        $sql = "SELECT ID, Modelo FROM PORTATIL WHERE Estado = 'disponible' ORDER BY Modelo";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPortatilPorId(int $portatilId): ?array
    {
        $sql = "SELECT ID, Modelo, Estado FROM PORTATIL WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $portatilId]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }

   
    public function registrarPrestamo(array $datos): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sqlVerificar = "SELECT Estado FROM PORTATIL WHERE ID = :portatilId FOR UPDATE";
            $consultaVerificar = $this->conexion->prepare($sqlVerificar);
            $consultaVerificar->execute(["portatilId" => $datos["portatilId"]]);
            $portatil = $consultaVerificar->fetch(PDO::FETCH_ASSOC);

            if ($portatil === false || $portatil["Estado"] !== "disponible") {
                $this->conexion->rollBack();
                return false;
            }

            $sqlPrestamo = "INSERT INTO PRESTAMO 
                (FechaPrestamo, FechaDev, Estado, CIAlumno, Clase, CorreoAlumno, TelefonoAlumno, SolicitanteCedula, PortatilID)
                VALUES (CURDATE(), :fechaDev, 'activo', :ciAlumno, :clase, :correoAlumno, :telefonoAlumno, :cedula, :portatilId)";

            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
            $consultaPrestamo->execute([
                "fechaDev" => $datos["fechaDev"],
                "ciAlumno" => $datos["ciAlumno"],
                "clase" => $datos["clase"],
                "correoAlumno" => $datos["correoAlumno"],
                "telefonoAlumno" => $datos["telefonoAlumno"],
                "cedula" => $datos["cedula"],
                "portatilId" => $datos["portatilId"],
            ]);

            $sqlActualizarPortatil = "UPDATE PORTATIL SET Estado = 'en_prestamo' WHERE ID = :portatilId";
            $this->conexion->prepare($sqlActualizarPortatil)->execute(["portatilId" => $datos["portatilId"]]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function finalizarPrestamo(int $prestamoId): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sqlBuscar = "SELECT PortatilID, Estado FROM PRESTAMO WHERE ID = :id FOR UPDATE";
            $consultaBuscar = $this->conexion->prepare($sqlBuscar);
            $consultaBuscar->execute(["id" => $prestamoId]);
            $prestamo = $consultaBuscar->fetch(PDO::FETCH_ASSOC);

            if ($prestamo === false || $prestamo["Estado"] !== "activo") {
                $this->conexion->rollBack();
                return false;
            }

            $this->conexion->prepare("UPDATE PRESTAMO SET Estado = 'finalizado' WHERE ID = :id")
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

    public function modificarDatosAlumno(int $prestamoId, string $ciAlumno, string $clase, string $correoAlumno, string $telefonoAlumno): bool
    {
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

    public function eliminarPrestamo(int $prestamoId): bool
    {
        $sql = "DELETE FROM PRESTAMO WHERE ID = :id AND Estado = 'finalizado'";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $prestamoId]);
        return $consulta->rowCount() > 0;
    }

    public function listarPrestamos(string $cedula): array
    {
        $sql = "SELECT PRESTAMO.ID, PRESTAMO.FechaPrestamo, PRESTAMO.FechaDev, PRESTAMO.Estado,
                       PRESTAMO.CIAlumno, PRESTAMO.Clase, PRESTAMO.CorreoAlumno, PRESTAMO.TelefonoAlumno,
                       PORTATIL.Modelo AS PortatilModelo
                FROM PRESTAMO
                JOIN PORTATIL ON PORTATIL.ID = PRESTAMO.PortatilID
                WHERE PRESTAMO.SolicitanteCedula = :cedula
                ORDER BY PRESTAMO.Estado, PRESTAMO.FechaPrestamo DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPrestamoPorId(int $prestamoId): ?array
    {
        $sql = "SELECT * FROM PRESTAMO WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $prestamoId]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }
}
?>