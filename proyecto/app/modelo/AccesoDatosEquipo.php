<?php
class AccesoDatosEquipo {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

 
    private function obtenerUltimoNumero(int $aulaId): int {
        $sql = "SELECT NumPc FROM PC WHERE AulaID = :aulaId";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["aulaId" => $aulaId]);

        $numeros = $consulta->fetchAll(PDO::FETCH_COLUMN); 

        $maximo = 0;
        foreach ($numeros as $numPc) {
            $partes = explode("-", $numPc); //como el cut en bash 
            $numero = (int) end($partes); 
            if ($numero > $maximo) {
                $maximo = $numero;
            }
        }

        return $maximo; //enumero desde el numero mas grande de pc
    }

    
    public function agregarEquipos(int $aulaId, int $cantidad, string $modeloPc, string $monitor, string $modeloMouse, string $modeloTeclado): void {
        $ultimoNumero = $this->obtenerUltimoNumero($aulaId);

        $sqlPc = "INSERT INTO PC (NumPc, AulaID, Modelo, Monitor) VALUES (:numPc, :aulaId, :modelo, :monitor)";
        $consultaPc = $this->conexion->prepare($sqlPc);

        $sqlPeriferico = "INSERT INTO PERIFERICO (Tipo, Modelo, PcNumPc, PcAulaID) VALUES (:tipo, :modelo, :pcNumPc, :pcAulaId)";
        $consultaPeriferico = $this->conexion->prepare($sqlPeriferico);

        for ($i = 1; $i <= $cantidad; $i++) {
            $numeroActual = $ultimoNumero + $i;
            $numPc = "PC-" . str_pad($numeroActual, 2, "0", STR_PAD_LEFT); 

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
    }

    public function listarEquiposDeAula(int $aulaId) {
        $sql = "SELECT NumPc, Modelo, Monitor FROM PC WHERE AulaID = :aulaId ORDER BY NumPc";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["aulaId" => $aulaId]);

        $pcs = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $sqlPerifericos = "SELECT Tipo, Modelo FROM PERIFERICO WHERE PcNumPc = :numPc AND PcAulaID = :aulaId";
        $consultaPerifericos = $this->conexion->prepare($sqlPerifericos);

        foreach ($pcs as &$pc) {
            $consultaPerifericos->execute(["numPc" => $pc["NumPc"], "aulaId" => $aulaId]);
            $pc["perifericos"] = $consultaPerifericos->fetchAll(PDO::FETCH_ASSOC);
        }

        return $pcs;
    }
}

?>