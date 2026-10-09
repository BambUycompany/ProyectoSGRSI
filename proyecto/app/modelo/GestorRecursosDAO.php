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

    
        public function existeAula(
            string $tipo,
            string $numero,
            int $excluirId = 0
        ): bool {

            $tabla = $tipo === "laboratorio" ? "LABORATORIO": "TALLER";
            $sql = "SELECT 1
                    FROM AULA a
                    INNER JOIN $tabla t ON t.AulaID = a.ID
                    WHERE a.Numero = :numero
                    AND a.ID <> :excluirId
                    LIMIT 1";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "numero" => $numero,
                "excluirId" => $excluirId
            ]);

            return $consulta->fetchColumn() !== false;
        }
            

    
public function crearAula(string $tipo, string $numero): bool {

    try {
        $this->conexion->beginTransaction();

        $sqlAula = "INSERT INTO AULA
                    (Numero, CantDispositivos)
                    VALUES (:numero, 0)";

        $consultaAula = $this->conexion->prepare($sqlAula);

        $consultaAula->execute([
            "numero" => $numero
        ]);

        $aulaId = (int)$this->conexion->lastInsertId();

        if ($tipo === "laboratorio") {
            $sqlTipo = "INSERT INTO LABORATORIO
                        (AulaID) VALUES (:aulaId)";
        } elseif ($tipo === "taller") {
            $sqlTipo = "INSERT INTO TALLER
                        (AulaID) VALUES (:aulaId)";
        } else {
            throw new InvalidArgumentException(
                "Tipo de aula inválido."
            );
        }
        $consultaTipo = $this->conexion->prepare($sqlTipo);
        $consultaTipo->execute([
            "aulaId" => $aulaId
        ]);
        $this->conexion->commit();

        return true;

    } catch (Throwable $error) {
        if ($this->conexion->inTransaction()) {
            $this->conexion->rollBack();
        }
        error_log("Error al crear aula: " . $error->getMessage());
        return false;
    }
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
        $sqlExiste = "SELECT ID FROM AULA
                      WHERE ID = :aulaId
                      FOR UPDATE";

        $consultaExiste = $this->conexion->prepare($sqlExiste);
        $consultaExiste->execute(["aulaId" => $aulaId]);

        if ($consultaExiste->fetchColumn() === false) {
            $this->conexion->rollBack();
            return false;
        }

        $sql = "DELETE FROM AULA WHERE ID = :aulaId";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["aulaId" => $aulaId]);

        $eliminada = $consulta->rowCount() > 0;

        $this->conexion->commit();

        return $eliminada;

    } catch (PDOException $error) {

        if ($this->conexion->inTransaction()) {
            $this->conexion->rollBack();
        }

        error_log(
            "Error al eliminar aula: " . $error->getMessage()
        );

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

        
    public function modificarEquipo(string $numPc,int $aulaId,string $modeloPc,string $monitor,string $modeloMouse,string $modeloTeclado): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlExiste = "SELECT 1 FROM PC
                        WHERE NumPc = :numPc
                            AND AulaID = :aulaId
                        FOR UPDATE";

            $consultaExiste = $this->conexion->prepare($sqlExiste);
            $consultaExiste->execute([
                "numPc" => $numPc,
                "aulaId" => $aulaId
            ]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            $sqlPc = "UPDATE PC
                    SET Modelo = :modelo,
                        Monitor = :monitor
                    WHERE NumPc = :numPc
                        AND AulaID = :aulaId";

            $consultaPc = $this->conexion->prepare($sqlPc);

            $consultaPc->execute([
                "modelo" => $modeloPc,
                "monitor" => $monitor,
                "numPc" => $numPc,
                "aulaId" => $aulaId
            ]);

            $sqlExistePeriferico = "SELECT ID
                                FROM PERIFERICO
                                WHERE PcNumPc = :numPc
                                    AND PcAulaID = :aulaId
                                    AND Tipo = :tipo
                                FOR UPDATE";

            $consultaExistePeriferico =
                $this->conexion->prepare($sqlExistePeriferico);

            $sqlActualizar = "UPDATE PERIFERICO
                            SET Modelo = :modelo
                            WHERE ID = :id";

            $consultaActualizar =
                $this->conexion->prepare($sqlActualizar);

            $sqlInsertar = "INSERT INTO PERIFERICO
                            (Tipo, Modelo, PcNumPc, PcAulaID)
                            VALUES
                            (:tipo, :modelo, :numPc, :aulaId)";

            $consultaInsertar =
                $this->conexion->prepare($sqlInsertar);

            $perifericos = [
                "mouse" => $modeloMouse,
                "teclado" => $modeloTeclado
            ];

            foreach ($perifericos as $tipo => $modelo) {

                $consultaExistePeriferico->execute([
                    "numPc" => $numPc,
                    "aulaId" => $aulaId,
                    "tipo" => $tipo
                ]);

                $ids = $consultaExistePeriferico->fetchAll(
                    PDO::FETCH_COLUMN
                );

                if (!empty($ids)) {

                    foreach ($ids as $id) {
                        $consultaActualizar->execute([
                            "modelo" => $modelo,
                            "id" => $id
                        ]);
                    }

                } else {

                    $consultaInsertar->execute([
                        "tipo" => $tipo,
                        "modelo" => $modelo,
                        "numPc" => $numPc,
                        "aulaId" => $aulaId
                    ]);
                }
            }

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log(
                "Error al modificar equipo: " . $error->getMessage()
            );

            return false;
        }
    }


    public function eliminarEquipo(string $numPc, int $aulaId): bool {
    try {
        $this->conexion->beginTransaction();

        $sqlPerifericos = "DELETE FROM PERIFERICO WHERE PcNumPc = :numPc AND PcAulaID = :aulaId";
        $consultaPerifericos = $this->conexion->prepare($sqlPerifericos);
        $consultaPerifericos->execute([
            "numPc" => $numPc,
            "aulaId" => $aulaId
        ]);

        $sqlPc = "DELETE FROM PC WHERE NumPc = :numPc AND AulaID = :aulaId";
        $consultaPc = $this->conexion->prepare($sqlPc);
        $consultaPc->execute([
            "numPc" => $numPc,
            "aulaId" => $aulaId
        ]);

        $filasEliminadas = $consultaPc->rowCount();

        $this->conexion->commit();

        return $filasEliminadas > 0;

    } catch (PDOException $e) {
        if ($this->conexion->inTransaction()) {
            $this->conexion->rollBack();
        }
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

   
    public function modificarPortatil(int $portatilId,string $modelo): bool {
        if ($this->obtenerPorId($portatilId) === null) {
            return false;
        }
        $sql = "UPDATE PORTATIL
                SET Modelo = :modelo
                WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            "modelo" => $modelo,
            "id" => $portatilId
        ]);
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