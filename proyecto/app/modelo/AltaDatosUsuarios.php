<?php

class AltaDatosUsuarios {
    private PDO $conexion;  

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function registrarUsuario(string $cedula, string $nombre, string $apellido, string $claveHash, string $rol) {

        try{

            $this->conexion->beginTransaction();

            $sqlUsuario = "INSERT INTO usuario (cedula, nombre, apellido, claveHash) VALUES (:cedula, :nombre, :apellido, :claveHash)";

            $consultaUsuario = $this->conexion->prepare($sqlUsuario);

            $consultaUsuario->execute(["cedula" => $cedula, "nombre" => $nombre, "apellido" => $apellido, "claveHash" => $claveHash]);

            switch ($rol) {
                case "administrador":
                    $sqlRol = "INSERT INTO administrador (cedula) VALUES (:cedula)";
                    break;
                case "soporte":
                    $sqlRol = "INSERT INTO soporte (cedula) VALUES (:cedula)";
                    break;
                case "solicitante":
                    $sqlRol = "INSERT INTO solicitante (cedula) VALUES (:cedula)";
                    break;
                default:
                    $this->conexion->rollBack();
                    return false;
            }
            
            $consultaRol = $this->conexion->prepare($sqlRol);

            $consultaRol->execute(["cedula" => $cedula]);

            $this->conexion->commit();

            return true;
        
        }catch (PDOException $e) {
            
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
        }
        return false;
        }

       
    }

}

   
?>