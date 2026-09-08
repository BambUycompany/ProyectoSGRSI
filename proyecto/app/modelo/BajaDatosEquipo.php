<?php
class BajaDatosEquipo {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Elimina una PC puntual. Sus periféricos se borran solos por ON DELETE CASCADE.
     * Si la PC tiene tickets asociados (historial de fallas), la base de datos
     * rechaza el borrado para proteger ese historial, y acá lo capturamos como false.
     */
    public function eliminarEquipo(string $numPc, int $aulaId): bool {
        try {
            $sql = "DELETE FROM PC WHERE NumPc = :numPc AND AulaID = :aulaId";
            $consulta = $this->conexion->prepare($sql);
            return $consulta->execute(["numPc" => $numPc, "aulaId" => $aulaId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

?>