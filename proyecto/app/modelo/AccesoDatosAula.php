<?php

class AccesoDatosAula {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }
    public function crearAula(string $tipo, string $numero) {
            $sql = "INSERT INTO AULA (Numero, CantDispositivos) VALUES (:numero, 0)";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute(["numero" => $numero]); 

            $aulaId = $this->conexion->lastInsertId(); 

            if ($tipo === "laboratorio") {
                $sqlTipo = "INSERT INTO LABORATORIO (AulaID) VALUES (:aulaId)";
            } else {
                $sqlTipo = "INSERT INTO TALLER (AulaID) VALUES (:aulaId)";
            }

            $consultaTipo = $this->conexion->prepare($sqlTipo);
            $consultaTipo->execute(["aulaId" => $aulaId]);

            return $aulaId;
        }

     public function listarAulasConDetalle() {
        $sql = "SELECT 
                    AULA.ID,
                    AULA.Numero,
                    CASE 
                        WHEN EXISTS (SELECT 1 FROM LABORATORIO WHERE LABORATORIO.AulaID = AULA.ID) THEN 'laboratorio'
                        ELSE 'taller'
                    END AS Tipo,
                    (SELECT COUNT(*) FROM PC WHERE PC.AulaID = AULA.ID) AS CantidadPcs
                FROM AULA
                ORDER BY Tipo, AULA.Numero";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC); 
    }

     public function obtenerAulaPorId(int $aulaId) {
        $sql = "SELECT 
                    AULA.ID,
                    AULA.Numero,
                    CASE 
                        WHEN EXISTS (SELECT 1 FROM LABORATORIO WHERE LABORATORIO.AulaID = AULA.ID) THEN 'laboratorio'
                        ELSE 'taller'
                    END AS Tipo
                FROM AULA
                WHERE AULA.ID = :aulaId";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["aulaId" => $aulaId]);

        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }

}