<?php

class AltaDatosPortail{

        private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function crearPortatil(string $modelo): bool {
        $sql = "INSERT INTO PORTATIL (Modelo, Estado) VALUES (:modelo, 'disponible')";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute(["modelo" => $modelo]);
    }
}