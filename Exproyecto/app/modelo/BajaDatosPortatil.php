<?php

class BajaDatosPortatil {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function eliminarPortatil(int $portatilId): bool {
        try {
            $sql = "DELETE FROM PORTATIL WHERE ID = :id AND Estado = 'disponible'";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute(["id" => $portatilId]);
            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}

?>