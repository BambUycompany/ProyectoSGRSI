<?php
class AccesoDatosTickets {
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
}   