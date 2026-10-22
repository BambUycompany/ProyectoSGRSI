
<?php

class NotificacionDAO {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;

        $this->conexion->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function registrarNotificacion(
        int $ticketId
    ): bool {

        if ($ticketId <= 0) {
            return false;
        }

        $sql = "INSERT INTO notificacion (
                    TicketID,
                    DestinatarioCedula,
                    Leida,
                    FechaCreacion
                )
                SELECT
                    t.ID,
                    t.SolicitanteCedula,
                    0,
                    NOW()
                FROM ticket t
                WHERE t.ID = :ticketId
                  AND t.Estado = 'finalizado'
                  AND t.SolicitanteCedula IS NOT NULL
                  AND TRIM(t.SolicitanteCedula) <> ''";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "ticketId" => $ticketId
        ]);

        return $consulta->rowCount() === 1;
    }

 
    public function listarPorDestinatario(
        string $cedula,
        int $limite = 50
    ): array {

        $cedula = trim($cedula);

        if ($cedula === "") {
            return [];
        }

        $limite = max(1, min($limite, 100));

        $sql = "SELECT
                    n.ID,
                    n.TicketID,
                    n.Leida,
                    n.FechaCreacion,

                    t.PcNumPc,
                    t.PcAulaID,
                    t.PlanillaId,
                    t.Incidente AS Diagnostico,

                    a.Numero AS AulaNumero,

                    CASE
                        WHEN l.AulaID IS NOT NULL
                            THEN 'laboratorio'
                        ELSE 'taller'
                    END AS AulaTipo

                FROM notificacion n

                INNER JOIN ticket t
                    ON t.ID = n.TicketID

                INNER JOIN aula a
                    ON a.ID = t.PcAulaID

                LEFT JOIN laboratorio l
                    ON l.AulaID = a.ID

                WHERE n.DestinatarioCedula = :cedula
                  AND t.SolicitanteCedula = :cedulaTicket
                  AND t.Estado = 'finalizado'

                ORDER BY
                    n.FechaCreacion DESC,
                    n.ID DESC

                LIMIT {$limite}";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula,
            "cedulaTicket" => $cedula
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function contarNoLeidas(
        string $cedula
    ): int {

        $cedula = trim($cedula);

        if ($cedula === "") {
            return 0;
        }

        $sql = "SELECT COUNT(*)
                FROM notificacion n

                INNER JOIN ticket t
                    ON t.ID = n.TicketID

                WHERE n.DestinatarioCedula = :cedula
                  AND t.SolicitanteCedula = :cedulaTicket
                  AND t.Estado = 'finalizado'
                  AND n.Leida = 0";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula,
            "cedulaTicket" => $cedula
        ]);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Marca como leída una notificación.
     *
     * Solo puede modificarse si pertenece
     * al usuario indicado.
     */
    public function marcarComoLeida(
        int $notificacionId,
        string $cedula
    ): bool {

        $cedula = trim($cedula);

        if ($notificacionId <= 0 || $cedula === "") {
            return false;
        }

        $sql = "UPDATE notificacion
                SET Leida = 1
                WHERE ID = :id
                  AND DestinatarioCedula = :cedula
                  AND TicketID IN (
                      SELECT t.ID
                      FROM ticket t
                      WHERE t.SolicitanteCedula = :cedulaTicket
                        AND t.Estado = 'finalizado'
                  )";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "id" => $notificacionId,
            "cedula" => $cedula,
            "cedulaTicket" => $cedula
        ]);

        if ($consulta->rowCount() > 0) {
            return true;
        }

    
        $sqlVerificacion = "SELECT COUNT(*)
                            FROM notificacion n

                            INNER JOIN ticket t
                                ON t.ID = n.TicketID

                            WHERE n.ID = :id
                              AND n.DestinatarioCedula = :cedula
                              AND t.SolicitanteCedula = :cedulaTicket
                              AND t.Estado = 'finalizado'
                              AND n.Leida = 1";

        $verificacion = $this->conexion->prepare(
            $sqlVerificacion
        );

        $verificacion->execute([
            "id" => $notificacionId,
            "cedula" => $cedula,
            "cedulaTicket" => $cedula
        ]);

        return (int) $verificacion->fetchColumn() > 0;
    }
}
