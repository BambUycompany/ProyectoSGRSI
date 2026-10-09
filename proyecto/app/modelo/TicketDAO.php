
<?php

class TicketDAO {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;

        $this->conexion->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function listarTicketsAgrupados(): array {

        $sql = "SELECT
                    t.PcNumPc,
                    t.PcAulaID,
                    a.Numero AS AulaNumero,
                    CASE
                        WHEN l.AulaID IS NOT NULL THEN 'laboratorio'
                        ELSE 'taller'
                    END AS AulaTipo,
                    COUNT(*) AS CantidadReportes
                FROM TICKET t
                INNER JOIN AULA a ON a.ID = t.PcAulaID
                LEFT JOIN LABORATORIO l ON l.AulaID = a.ID
                GROUP BY
                    t.PcNumPc,
                    t.PcAulaID,
                    a.Numero,
                    l.AulaID
                ORDER BY
                    CantidadReportes DESC,
                    a.Numero ASC,
                    t.PcNumPc ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTicketsPorPcYAula(
        string $numPc,
        int $aulaId
    ): array {

        $sql = "SELECT
                    t.ID,
                    t.Descripcion,
                    t.Fallo,
                    t.Estado,
                    t.Prioridad,
                    t.Incidente,
                    t.FechaCreacion,
                    t.PcNumPc,
                    t.PcAulaID,
                    t.PlanillaId,
                    a.Numero AS AulaNumero,
                    CASE
                        WHEN l.AulaID IS NOT NULL THEN 'laboratorio'
                        ELSE 'taller'
                    END AS AulaTipo
                FROM TICKET t
                INNER JOIN AULA a ON a.ID = t.PcAulaID
                LEFT JOIN LABORATORIO l ON l.AulaID = a.ID
                WHERE t.PcNumPc = :numPc
                  AND t.PcAulaID = :aulaId
                ORDER BY
                    t.FechaCreacion DESC,
                    t.ID DESC";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "numPc" => $numPc,
            "aulaId" => $aulaId
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTicketPorId(int $id): ?array {

        $sql = "SELECT
                    t.ID,
                    t.Descripcion,
                    t.Fallo,
                    t.Estado,
                    t.Prioridad,
                    t.Incidente,
                    t.FechaCreacion,
                    t.PcNumPc,
                    t.PcAulaID,
                    t.PlanillaId
                FROM TICKET t
                WHERE t.ID = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "id" => $id
        ]);

        $ticket = $consulta->fetch(PDO::FETCH_ASSOC);

        return $ticket === false ? null : $ticket;
    }

    public function listarTicketsDePlanilla(
        int $planillaId
    ): array {

        $sql = "SELECT
                    t.ID,
                    t.Descripcion,
                    t.Fallo,
                    t.Estado,
                    t.Prioridad,
                    t.Incidente,
                    t.FechaCreacion,
                    t.PcNumPc,
                    t.PcAulaID,
                    t.PlanillaId
                FROM TICKET t
                WHERE t.PlanillaId = :planillaId
                ORDER BY
                    t.FechaCreacion ASC,
                    t.ID ASC";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "planillaId" => $planillaId
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarTicket(array $datos): bool {

        $sql = "INSERT INTO TICKET (
                    Descripcion,
                    Fallo,
                    Estado,
                    FechaCreacion,
                    PcNumPc,
                    PcAulaID,
                    SolicitanteCedula,
                    PlanillaId
                )
                VALUES (
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
                "No se pudo registrar el ticket."
            );
        }

        return true;
    }

    public function cambiarEstadoTicket(
        int $id,
        string $nuevoEstado
    ): bool {

        $estadosPermitidos = [
            "pendiente",
            "en proceso"
        ];

        if (!in_array($nuevoEstado, $estadosPermitidos, true)) {
            return false;
        }

        $sql = "UPDATE TICKET
                SET Estado = :estado
                WHERE ID = :id
                  AND Estado <> 'finalizado'";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "estado" => $nuevoEstado,
            "id" => $id
        ]);

        if ($consulta->rowCount() > 0) {
            return true;
        }

        $ticket = $this->obtenerTicketPorId($id);

        return $ticket !== null &&
               $ticket["Estado"] === $nuevoEstado;
    }

    public function cambiarPrioridadTicket(
        int $id,
        string $nuevaPrioridad
    ): bool {

        $prioridadesPermitidas = [
            "baja",
            "media",
            "alta"
        ];

        if (!in_array(
            $nuevaPrioridad,
            $prioridadesPermitidas,
            true
        )) {
            return false;
        }

        $sql = "UPDATE TICKET
                SET Prioridad = :prioridad
                WHERE ID = :id
                  AND Estado <> 'finalizado'";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "prioridad" => $nuevaPrioridad,
            "id" => $id
        ]);

        if ($consulta->rowCount() > 0) {
            return true;
        }

        $ticket = $this->obtenerTicketPorId($id);

        return $ticket !== null &&
               $ticket["Estado"] !== "finalizado" &&
               $ticket["Prioridad"] === $nuevaPrioridad;
    }

    public function finalizarTicket(
        int $id,
        string $diagnostico,
        string $soporteCedula
    ): bool {

        $diagnostico = trim($diagnostico);

        if (
            $id <= 0 ||
            $diagnostico === "" ||
            $soporteCedula === ""
        ) {
            return false;
        }

        $sql = "UPDATE TICKET
                SET Estado = 'finalizado',
                    Incidente = :diagnostico
                WHERE ID = :id
                  AND Estado <> 'finalizado'";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "diagnostico" => $diagnostico,
            "id" => $id
        ]);

        return $consulta->rowCount() > 0;
    }
}
