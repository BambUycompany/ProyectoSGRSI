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
    
public function registrarTicket(array $datos): bool {

    $sql = "INSERT INTO TICKET
            (
                Descripcion,
                Fallo,
                Estado,
                FechaCreacion,
                PcNumPc,
                PcAulaID,
                SolicitanteCedula,
                PlanillaId
            )
            VALUES
            (
                :descripcion,
                :fallo,
                'pendiente',
                NOW(),
                :numPc,
                :aulaId,
                :cedula,
                :planillaId
            )";

    $consulta = $this->conexion->prepare($sql);

   
    $resultado = $consulta->execute([
        "descripcion" => $datos["descripcion"],
        "fallo" => $datos["fallo"],
        "numPc" => $datos["numeroPc"],
        "aulaId" => $datos["aulaId"],
        "cedula" => $datos["documentoRegistrante"],
        "planillaId" => $datos["planillaId"]
    ]);

    if (!$resultado) {
        throw new RuntimeException(
            "No se pudo insertar el ticket."
        );
    }

    return true;
}


    public function actualizarTicket(int $ticketId, string $campo, string $valor): bool
    {
        $camposPermitidos = ['prioridad', 'estado', 'diagnostico'];
        if (!in_array($campo, $camposPermitidos)) {
            return false;
        }

        try {
            $sql = "UPDATE TICKET SET $campo = :valor WHERE ID = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':valor' => $valor,
                ':id' => $ticketId
            ]);
        } catch (PDOException $e) {
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