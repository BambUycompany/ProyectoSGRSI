<?php

class ModificarDatosAula {

    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
     */
    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Modifica el número y tipo de un aula existente.
     *
     * @param int $aulaId ID único del aula a modificar.
     * @param string $tipo Nuevo tipo ('laboratorio' o 'taller').
     * @param string $numero Nuevo número de aula.
     *
     * @return bool TRUE si la modificación se realiza correctamente, FALSE en caso contrario.
     */
    public function modificarAula(int $aulaId, string $tipo, string $numero): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlAula = "UPDATE AULA SET Numero = :numero WHERE ID = :aulaId";
            $consultaAula = $this->conexion->prepare($sqlAula);
            $consultaAula->execute([
                "numero" => $numero,
                "aulaId" => $aulaId
            ]);

            $sqlDelLab = "DELETE FROM LABORATORIO WHERE AulaID = :aulaId";
            $consultaDelLab = $this->conexion->prepare($sqlDelLab);
            $consultaDelLab->execute(["aulaId" => $aulaId]);

            $sqlDelTal = "DELETE FROM TALLER WHERE AulaID = :aulaId";
            $consultaDelTal = $this->conexion->prepare($sqlDelTal);
            $consultaDelTal->execute(["aulaId" => $aulaId]);

            if (strtolower(trim($tipo)) === "laboratorio") {
                $sqlSubtipo = "INSERT INTO LABORATORIO (AulaID) VALUES (:aulaId)";
            } else {
                $sqlSubtipo = "INSERT INTO TALLER (AulaID) VALUES (:aulaId)";
            }

            $consultaSubtipo = $this->conexion->prepare($sqlSubtipo);
            $consultaSubtipo->execute(["aulaId" => $aulaId]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }
}
?>