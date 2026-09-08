<?php

class AltaDatosEquipo {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Busca el número más alto de PC ya usado en esa aula
     * para saber desde dónde seguir numerando las nuevas.
     */
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

    /**
     * Crea $cantidad PCs idénticas en la misma aula, autonumeradas, cada una con su mouse y teclado.
     */
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
}

?>