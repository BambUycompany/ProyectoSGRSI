<?php

class AccesoDatosPrestamo{


  
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function listarPrestamos(string $cedula) {
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

    public function obtenerPorId(int $prestamoId) {
        $sql = "SELECT * FROM PRESTAMO WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $prestamoId]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }
        

    
}