<?php
class GestorRecursosDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
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

    public function listarAulas(): array {
         $sql = "SELECT 
                AULA.ID AS ID,
                AULA.Numero AS Numero,
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

    public function crearAula(string $tipo, string $numero): bool {
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

    public function modificarAula(int $aulaId, string $tipo, string $numero): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlAula = "UPDATE AULA SET Numero = :numero WHERE ID = :aulaId";
            $consultaAula = $this->conexion->prepare($sqlAula);
            $consultaAula->execute([
                "numero" => $numero,
                "aulaId" => $aulaId
            ]);

            $sqlDelLab = "DELETE FROM LABORATORIO WHERE AulaID = :aulaId";
            $consultaDelLab = $this->conexion->prepare($sqlDelLab);
            $consultaDelLab->execute(["aulaId" => $aulaId]);

            $sqlDelTal = "DELETE FROM TALLER WHERE AulaID = :aulaId";
            $consultaDelTal = $this->conexion->prepare($sqlDelTal);
            $consultaDelTal->execute(["aulaId" => $aulaId]);

            if (strtolower(trim($tipo)) === "laboratorio") {
                $sqlSubtipo = "INSERT INTO LABORATORIO (AulaID) VALUES (:aulaId)";
            } else {
                $sqlSubtipo = "INSERT INTO TALLER (AulaID) VALUES (:aulaId)";
            }

            $consultaSubtipo = $this->conexion->prepare($sqlSubtipo);
            $consultaSubtipo->execute(["aulaId" => $aulaId]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function eliminarAula(int $aulaId): bool {
         try {
            $this->conexion->beginTransaction();

            $sqlLab = "DELETE FROM LABORATORIO WHERE AulaID = :aulaId";
            $consultaLab = $this->conexion->prepare($sqlLab);
            $consultaLab->execute(["aulaId" => $aulaId]);

            $sqlTal = "DELETE FROM TALLER WHERE AulaID = :aulaId";
            $consultaTal = $this->conexion->prepare($sqlTal);
            $consultaTal->execute(["aulaId" => $aulaId]);

            $sqlAula = "DELETE FROM AULA WHERE ID = :aulaId";
            $consultaAula = $this->conexion->prepare($sqlAula);
            $consultaAula->execute(["aulaId" => $aulaId]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
        
    }

//

    public function listarEquiposDeAula(int $aulaId) {
        $sql = "SELECT NumPc, Modelo, Monitor FROM PC WHERE AulaID = :aulaId ORDER BY NumPc";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["aulaId" => $aulaId]);
        $pcs = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $sqlPerifericos = "SELECT Tipo, Modelo, PcNumPc FROM PERIFERICO WHERE PcAulaID = :aulaId";
        $consultaPerifericos = $this->conexion->prepare($sqlPerifericos);
        $consultaPerifericos->execute(["aulaId" => $aulaId]);
        $todosLosPerifericos = $consultaPerifericos->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pcs as &$pc) {
            $pc["Mouse"] = "";
            $pc["Teclado"] = "";

            foreach ($todosLosPerifericos as $periferico) {
                if ($periferico["PcNumPc"] === $pc["NumPc"]) {
                    if ($periferico["Tipo"] === "mouse") {
                        $pc["Mouse"] = $periferico["Modelo"];
                    } elseif ($periferico["Tipo"] === "teclado") {
                        $pc["Teclado"] = $periferico["Modelo"];
                    }
                }
            }
        }

        return $pcs;
    }

    private function obtenerUltimoNumero(int $aulaId): int {
        $sql = "SELECT NumPc FROM PC WHERE AulaID = :aulaId";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["aulaId" => $aulaId]);

        $numeros = $consulta->fetchAll(PDO::FETCH_COLUMN);

        $maximo = 0;
        foreach ($numeros as $numPc) {
            $partes = explode("-", $numPc);
            $numero = (int) end($partes);
            if ($numero > $maximo) {
                $maximo = $numero;
            }
        }

        return $maximo;
    }

    public function agregarEquipos(int $aulaId, int $cantidad, string $modeloPc, string $monitor, string $modeloMouse, string $modeloTeclado): bool {
        try {
            $this->conexion->beginTransaction();

            $ultimoNumero = $this->obtenerUltimoNumero($aulaId);

            $sqlPc = "INSERT INTO PC (NumPc, AulaID, Modelo, Monitor) VALUES (:numPc, :aulaId, :modelo, :monitor)";
            $consultaPc = $this->conexion->prepare($sqlPc);

            $sqlPeriferico = "INSERT INTO PERIFERICO (Tipo, Modelo, PcNumPc, PcAulaID) VALUES (:tipo, :modelo, :pcNumPc, :pcAulaId)";
            $consultaPeriferico = $this->conexion->prepare($sqlPeriferico);

            for ($i = 1; $i <= $cantidad; $i++) {
                $numeroActual = $ultimoNumero + $i;
                $numPc = "PC-" . str_pad((string) $numeroActual, 2, "0", STR_PAD_LEFT);

                $consultaPc->execute([
                    "numPc" => $numPc,
                    "aulaId" => $aulaId,
                    "modelo" => $modeloPc,
                    "monitor" => $monitor,
                ]);

                $consultaPeriferico->execute([
                    "tipo" => "mouse",
                    "modelo" => $modeloMouse,
                    "pcNumPc" => $numPc,
                    "pcAulaId" => $aulaId,
                ]);

                $consultaPeriferico->execute([
                    "tipo" => "teclado",
                    "modelo" => $modeloTeclado,
                    "pcNumPc" => $numPc,
                    "pcAulaId" => $aulaId,
                ]);
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

        public function modificarEquipo(string $numPc, int $aulaId, string $modeloPc, string $monitor, string $modeloMouse, string $modeloTeclado): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlPc = "UPDATE PC SET Modelo = :modelo, Monitor = :monitor WHERE NumPc = :numPc AND AulaID = :aulaId";
            $consultaPc = $this->conexion->prepare($sqlPc);
            $consultaPc->execute([
                "modelo" => $modeloPc,
                "monitor" => $monitor,
                "numPc" => $numPc,
                "aulaId" => $aulaId,
            ]);

            $sqlPeriferico = "UPDATE PERIFERICO SET Modelo = :modelo WHERE PcNumPc = :numPc AND PcAulaID = :aulaId AND Tipo = :tipo";
            $consultaPeriferico = $this->conexion->prepare($sqlPeriferico);

            $consultaPeriferico->execute([
                "modelo" => $modeloMouse,
                "numPc" => $numPc,
                "aulaId" => $aulaId,
                "tipo" => "mouse",
            ]);

            $consultaPeriferico->execute([
                "modelo" => $modeloTeclado,
                "numPc" => $numPc,
                "aulaId" => $aulaId,
                "tipo" => "teclado",
            ]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function eliminarEquipo(string $numPc, int $aulaId): bool {
        try {
            $sql = "DELETE FROM PC WHERE NumPc = :numPc AND AulaID = :aulaId";
            $consulta = $this->conexion->prepare($sql);
            return $consulta->execute(["numPc" => $numPc, "aulaId" => $aulaId]);
        } catch (PDOException $e) {
            return false;
        }
    }

//

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

    public function crearPortatil(string $modelo): bool {
        $sql = "INSERT INTO PORTATIL (Modelo, Estado) VALUES (:modelo, 'disponible')";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute(["modelo" => $modelo]);
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

    public function habilitarPortatil(int $portatilId): bool {
        $sql = "UPDATE PORTATIL SET Estado = 'disponible' WHERE ID = :id AND Estado = 'deshabilitado'";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $portatilId]);
        return $consulta->rowCount() > 0;
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