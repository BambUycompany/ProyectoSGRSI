<?php
class ModificarDatosEquipo {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Actualiza el modelo/monitor de una PC puntual, y el modelo de su mouse y teclado.
     * Asume que cada PC tiene exactamente un periférico de tipo 'mouse' y uno de tipo 'teclado'
     * (así es como los creamos en AltaDatosEquipo).
     */
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
}

?>