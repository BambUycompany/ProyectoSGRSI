<?php
class AccesoDatosEquipo {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }


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
}

?>
