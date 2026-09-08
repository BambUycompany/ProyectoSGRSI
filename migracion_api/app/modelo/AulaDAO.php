<?php

class AulaDAO {
    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     *
     * @param PDO $conexion La conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
     */

    class AulaDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function crearAula(string $tipo, string $numero) {
        try {
            $this->conexion->beginTransaction();

            $sql = "INSERT INTO AULA (Numero, CantDispositivos) VALUES (:numero, 0)";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute(["numero" => $numero]);

            $aulaId = $this->conexion->lastInsertId();

            if (strtolower($tipo) === "laboratorio") {
                $sqlTipo = "INSERT INTO LABORATORIO (AulaID) VALUES (:aulaId)";
            } else {
                $sqlTipo = "INSERT INTO TALLER (AulaID) VALUES (:aulaId)";
            }

            $consultaTipo = $this->conexion->prepare($sqlTipo);
            $consultaTipo->execute(["aulaId" => $aulaId]);

            $this->conexion->commit();
            return $aulaId;
        } catch (Exception $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function modificarAula(int $aulaId, string $tipo, string $numero): bool {
        try {
            $this->conexion->beginTransaction();

            // Actualizar número en la tabla base
            $sqlAula = "UPDATE AULA SET Numero = :numero WHERE ID = :aulaId";
            $consultaAula = $this->conexion->prepare($sqlAula);
            $consultaAula->execute([
                "numero" => $numero,
                "aulaId" => $aulaId
            ]);

            // Limpiar subtipos existentes
            $sqlDelLab = "DELETE FROM LABORATORIO WHERE AulaID = :aulaId";
            $consultaDelLab = $this->conexion->prepare($sqlDelLab);
            $consultaDelLab->execute(["aulaId" => $aulaId]);

            $sqlDelTal = "DELETE FROM TALLER WHERE AulaID = :aulaId";
            $consultaDelTal = $this->conexion->prepare($sqlDelTal);
            $consultaDelTal->execute(["aulaId" => $aulaId]);

            // Insertar en la tabla de subtipo correspondiente
            if (strtolower($tipo) === "laboratorio") {
                $sqlIns = "INSERT INTO LABORATORIO (AulaID) VALUES (:aulaId)";
            } else {
                $sqlIns = "INSERT INTO TALLER (AulaID) VALUES (:aulaId)";
            }

            $consultaIns = $this->conexion->prepare($sqlIns);
            $consultaIns->execute(["aulaId" => $aulaId]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function eliminarAula(int $aulaId): bool {
        try {
            $sql = "DELETE FROM AULA WHERE ID = :aulaId";
            $consulta = $this->conexion->prepare($sql);
            return $consulta->execute(["aulaId" => $aulaId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function listarAulasConDetalle(): array {
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

    public function obtenerAulaPorId(int $aulaId): ?array {
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

    public function existeAula(string $tipo, string $numero): bool {
        $sql = "SELECT 1 
                FROM AULA 
                WHERE Numero = :numero 
                  AND (
                    (:tipo = 'laboratorio' AND EXISTS (SELECT 1 FROM LABORATORIO WHERE AulaID = AULA.ID))
                    OR
                    (:tipo = 'taller' AND EXISTS (SELECT 1 FROM TALLER WHERE AulaID = AULA.ID))
                  )";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            "numero" => $numero,
            "tipo" => strtolower(trim($tipo))
        ]);

        return $consulta->fetch() !== false;
    }
}
}