<?php
class AccesoDatosPortatil{
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodos(){
        $sql = "SELECT ID, Modelo, Estado FROM PORTATIL ORDER BY Modelo";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarDisponibles(){
        $sql = "SELECT ID, Modelo FROM PORTATIL WHERE Estado = 'disponible' ORDER BY Modelo";        
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);

    }

    public function obtenerPorId(int $portatilId) {
        $sql = "SELECT ID, Modelo, Estado FROM PORTATIL WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $portatilId]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }
}