<?php

class ModificarDatosPortatil {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function modificarPortatil(int $portatilId, string $modelo): bool {
        $sql = "UPDATE PORTATIL SET Modelo = :modelo WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute(["modelo" => $modelo, "id" => $portatilId]);
    }
}

?>