<?php

class AltaDatosPrestamo{

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }
    public function registrarPrestamo(array $datos): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlVerificar = "SELECT Estado FROM PORTATIL WHERE ID = :portatilId FOR UPDATE";
            $consultaVerificar = $this->conexion->prepare($sqlVerificar);
            $consultaVerificar->execute(["portatilId" => $datos["portatilId"]]);
            $portatil = $consultaVerificar->fetch(PDO::FETCH_ASSOC);

            if ($portatil === false || !in_array($portatil["Estado"], ["disponible", "devuelto"], true)) {
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

            $sqlActualizarPortatil = "UPDATE PORTATIL SET Estado = 'prestado' WHERE ID = :portatilId";
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
}


