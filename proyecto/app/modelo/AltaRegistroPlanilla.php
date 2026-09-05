<?php

/**
 * Clase encargada de registrar planillas de uso de aulas/laboratorios
 * en el sistema.
 */
class AltaRegistroPlanilla{
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Registra una nueva planilla con los datos de uso del espacio.
     *
     * @param string $tipo Tipo de espacio utilizado (por ejemplo: "laboratorio" o "taller").
     * @param string $numero Número identificador del espacio.
     * @param string $fecha Fecha en la que se utilizó el espacio.
     * @param string $horaEntrada Hora de entrada al espacio.
     * @param string $horaSalida Hora de salida del espacio.
     * @param string $solicitante Nombre del solicitante del espacio.
     * @param string $asignatura Asignatura para la cual se utilizó el espacio.
     * @param string $grupo Grupo que utilizó el espacio.
     * @param string $turno Turno en el que se utilizó el espacio.
     *
     * @return bool TRUE si el registro se realiza correctamente, FALSE en caso contrario.
     */
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