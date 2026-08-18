<?php
class AccesoDatosPlanilla {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function registrarPlanilla(array $datos) {
        $sql = "INSERT INTO PLANILLA 
            (Fecha, HoraEntrada, HoraSalida, Grupo, Turno, Asignatura, nombreSolicitante, cedulaRegistrante, AulaID)
            VALUES (:fecha, :horaEntrada, :horaSalida, :grupo, :turno, :asignatura, :nombreSolicitante, :cedulaRegistrante, :aulaId)";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "fecha" => $datos["fecha"],
            "horaEntrada" => $datos["horaEntrada"],
            "horaSalida" => $datos["horaSalida"],
            "grupo" => $datos["grupo"],
            "turno" => $datos["turno"],
            "asignatura" => $datos["asignatura"],
            "nombreSolicitante" => $datos["nombreSolicitante"],
            "cedulaRegistrante" => $datos["documentoRegistrante"],
            "aulaId" => $datos["aulaId"],
        ]);

        return $this->conexion->lastInsertId();
    }

    public function registrarTicket(array $datos) {
        $sql = "INSERT INTO TICKET 
            (Descripcion, Fallo, Estado, FechaCreacion, PcNumPc, PcAulaID, SolicitanteCedula, PlanillaId)
            VALUES (:descripcion, :fallo, 'pendiente', NOW(), :pcNumPc, :pcAulaID, :solicitanteCedula, :planillaId)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            "descripcion" => $datos["descripcion"],
            "fallo" => $datos["fallo"],
            "pcNumPc" => $datos["numeroPc"],
            "pcAulaID" => $datos["aulaId"],
            "solicitanteCedula" => $datos["documentoRegistrante"],
            "planillaId" => $datos["planillaId"],
        ]);
    }

public function listarAulas(){
        $sql = "SELECT AULA.Numero, 'laboratorio' AS Tipo 
                FROM AULA JOIN LABORATORIO ON AULA.ID = LABORATORIO.AulaID
                UNION ALL
                SELECT AULA.Numero, 'taller' AS Tipo 
                FROM AULA JOIN TALLER ON AULA.ID = TALLER.AulaID";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);//devuelve la consulta en un array asociativo es decir cada espacio esta vinculado a una clave
    }

public function buscarAulaId(string $tipo, string $numero) {
    if ($tipo === "laboratorio") {
        $sql = "SELECT AULA.ID 
                FROM AULA 
                JOIN LABORATORIO ON AULA.ID = LABORATORIO.AulaID 
                WHERE AULA.Numero = :numero";
    } elseif ($tipo === "taller") {
        $sql = "SELECT AULA.ID 
                FROM AULA 
                JOIN TALLER ON AULA.ID = TALLER.AulaID 
                WHERE AULA.Numero = :numero";
    } else {
        return null;
    }

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute(["numero" => $numero]);

    $fila = $consulta->fetch(PDO::FETCH_ASSOC); 

    if ($fila === false) {
        return null; 
    }

    return $fila["ID"];
}
}

?>