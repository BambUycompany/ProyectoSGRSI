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

    public function deshabilitarPortatil(int $portatilId): bool {
    $sql = "UPDATE PORTATIL SET Estado = 'deshabilitado' WHERE ID = :id AND Estado = 'disponible'";
    $consulta = $this->conexion->prepare($sql);
    $consulta->execute(["id" => $portatilId]);
    return $consulta->rowCount() > 0;
}
}

?>