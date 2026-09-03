<?php
class AltaRegistroPlanilla{
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function registrarPlanilla(string $tipo, string $numero, string $fecha, string $horaEntrada, string $horaSalida, string $solicitante, string $asignatura, string $grupo, string $turno) {
        try {
            $sql = "INSERT INTO planilla (tipo, numero, fecha, horaEntrada, horaSalida, solicitante, asignatura, grupo, turno) 
                    VALUES (:tipo, :numero, :fecha, :horaEntrada, :horaSalida, :solicitante, :asignatura, :grupo, :turno)";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "tipo" => $tipo,
                "numero" => $numero,
                "fecha" => $fecha,
                "horaEntrada" => $horaEntrada,
                "horaSalida" => $horaSalida,
                "solicitante" => $solicitante,
                "asignatura" => $asignatura,
                "grupo" => $grupo,
                "turno" => $turno
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

}
?>