<?php
class BajaDatosAula {

    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     *
     * @param PDO $conexion Conexión activa a la base de datos.
     */
    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Elimina un aula y sus registros asociados en las tablas de subtipos.
     *
     * @param int $aulaId ID único del aula a eliminar.
     * @return bool TRUE si se eliminó correctamente, FALSE si ocurrió un error.
     */
    public function eliminarAula(int $aulaId): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlLab = "DELETE FROM LABORATORIO WHERE AulaID = :aulaId";
            $consultaLab = $this->conexion->prepare($sqlLab);
            $consultaLab->execute(["aulaId" => $aulaId]);

            $sqlTal = "DELETE FROM TALLER WHERE AulaID = :aulaId";
            $consultaTal = $this->conexion->prepare($sqlTal);
            $consultaTal->execute(["aulaId" => $aulaId]);

            $sqlAula = "DELETE FROM AULA WHERE ID = :aulaId";
            $consultaAula = $this->conexion->prepare($sqlAula);
            $consultaAula->execute(["aulaId" => $aulaId]);

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
