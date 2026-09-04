<?php
class TicketDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

   public function listarTicketsAgrupados() {
        $sql = "SELECT 
                    TICKET.PcNumPc,
                    TICKET.PcAulaID,
                    AULA.Numero AS AulaNumero,
                    CASE 
                        WHEN EXISTS (SELECT 1 FROM LABORATORIO WHERE LABORATORIO.AulaID = AULA.ID) THEN 'laboratorio'
                        ELSE 'taller'
                    END AS AulaTipo,
                    COUNT(*) AS CantidadReportes
                FROM TICKET
                JOIN AULA ON AULA.ID = TICKET.PcAulaID
                GROUP BY TICKET.PcNumPc, TICKET.PcAulaID, AULA.Numero, AULA.ID
                ORDER BY CantidadReportes DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTicketsPorPcYAula(string $numPc, int $aulaId,) {
        $sql = "SELECT TICKET.ID, TICKET.Descripcion, TICKET.Fallo, TICKET.Estado, TICKET.FechaCreacion,
                        TICKET.PcNumPc, TICKET.PcAulaID,
                        AULA.ID AS AulaID,
                        AULA.Numero AS AulaNumero,
                        CASE 
                            WHEN EXISTS (SELECT 1 FROM LABORATORIO WHERE LABORATORIO.AulaID = AULA.ID) THEN 'laboratorio'
                            ELSE 'taller'
                        END AS AulaTipo
                    FROM TICKET
                    JOIN AULA ON AULA.ID = TICKET.PcAulaID
                    WHERE PcNumPc = :numPc AND PcAulaID = :aulaId
                    ORDER BY FechaCreacion";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "numPc" => $numPc,
                "aulaId" => $aulaId,
            ]);

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public function registrarTicket(array $datos) {
        try{

        $this->conexion->beginTransaction(); 
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
        }catch(PDOException $error){
            if($this->conexion->inTransaction()){
                $this->conexion->rollBack();

            }
            return false;

        }
    }

 /**
     * Lista todos los tickets asociados a una planilla, ordenados por fecha de creación.
     *
     * @param int $planillaId ID de la planilla de la cual se quieren obtener los tickets.
     *
     * @return array Array que asocia con los datos de cada ticket (ID, Descripcion, Fallo,
     * Estado, PcNumPc, FechaCreacion).
     */
    public function listarTicketsDePlanilla(int $planillaId) {
        $sql = "SELECT ID, Descripcion, Fallo, Estado, PcNumPc, FechaCreacion
                FROM TICKET
                WHERE PlanillaId = :planillaId 
                ORDER BY FechaCreacion";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["planillaId" => $planillaId]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }   
}   