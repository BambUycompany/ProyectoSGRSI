
<?php

class MetricasDAO {

    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
        $this->conexion->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    private function filtroFecha(
        ?string $fechaInicio,
        ?string $fechaFin,
        string $columna
    ): string {

        if ($fechaInicio === null || $fechaFin === null) {
            return "";
        }

        return " AND DATE($columna) BETWEEN :fechaInicio AND :fechaFin";
    }

    private function parametrosFecha(
        ?string $fechaInicio,
        ?string $fechaFin
    ): array {

        if ($fechaInicio === null || $fechaFin === null) {
            return [];
        }

        return [
            "fechaInicio" => $fechaInicio,
            "fechaFin" => $fechaFin
        ];
    }

    public function contarTicketsEmitidos(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): int {

        $sql = "SELECT COUNT(*)
                FROM TICKET
                WHERE 1 = 1" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "FechaCreacion"
                );

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        return (int) $consulta->fetchColumn();
    }

    public function contarRegistrosEmitidos(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): int {

        $sql = "SELECT COUNT(*)
                FROM PLANILLA
                WHERE 1 = 1" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "Fecha"
                );

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        return (int) $consulta->fetchColumn();
    }

    public function contarTicketsPorEstado(
        string $estado,
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): int {

        $sql = "SELECT COUNT(*)
                FROM TICKET
                WHERE Estado = :estado" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "FechaCreacion"
                );

        $parametros = array_merge(
            ["estado" => $estado],
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn();
    }

    public function contarTicketsFinalizados(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): int {

        return $this->contarTicketsPorEstado(
            "finalizado",
            $fechaInicio,
            $fechaFin
        );
    }

    public function contarPorBajoRendimiento(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): int {

        $sql = "SELECT COUNT(*)
                FROM TICKET
                WHERE Fallo = 'bajo_rendimiento'" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "FechaCreacion"
                );

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        return (int) $consulta->fetchColumn();
    }

    public function contarPortatilesPrestadas(): int {

        $sql = "SELECT COUNT(*)
                FROM PORTATIL
                WHERE Estado = 'en_prestamo'";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return (int) $consulta->fetchColumn();
    }

    public function listarTopPcsReportadas(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): array {

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
                WHERE 1 = 1" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "t.FechaCreacion"
                ) . "
                GROUP BY
                    t.PcNumPc,
                    t.PcAulaID,
                    a.Numero,
                    l.AulaID
                ORDER BY
                    CantidadReportes DESC,
                    a.Numero,
                    t.PcNumPc
                LIMIT 5";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPcMasReportada(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): ?array {

        $pcs = $this->listarTopPcsReportadas(
            $fechaInicio,
            $fechaFin
        );

        return $pcs[0] ?? null;
    }

    public function listarTopAulasConIncidencias(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): array {

        $sql = "SELECT
                    a.ID,
                    a.Numero,
                    CASE
                        WHEN l.AulaID IS NOT NULL THEN 'laboratorio'
                        ELSE 'taller'
                    END AS Tipo,
                    COUNT(t.ID) AS CantidadTickets
                FROM AULA a
                INNER JOIN TICKET t ON t.PcAulaID = a.ID
                LEFT JOIN LABORATORIO l ON l.AulaID = a.ID
                WHERE 1 = 1" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "t.FechaCreacion"
                ) . "
                GROUP BY
                    a.ID,
                    a.Numero,
                    l.AulaID
                ORDER BY
                    CantidadTickets DESC,
                    a.Numero
                LIMIT 5";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAulaConMasIncidencias(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): ?array {

        $aulas = $this->listarTopAulasConIncidencias(
            $fechaInicio,
            $fechaFin
        );

        return $aulas[0] ?? null;
    }

    public function listarFallasFrecuentes(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): array {

        $sql = "SELECT
                    Fallo,
                    COUNT(*) AS CantidadReportes
                FROM TICKET
                WHERE 1 = 1" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "FechaCreacion"
                ) . "
                GROUP BY Fallo
                ORDER BY
                    CantidadReportes DESC,
                    Fallo ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPcMasVandalizada(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): ?array {

        $sql = "SELECT
                    t.PcNumPc,
                    t.PcAulaID,
                    a.Numero AS AulaNumero,
                    CASE
                        WHEN l.AulaID IS NOT NULL THEN 'laboratorio'
                        ELSE 'taller'
                    END AS AulaTipo,
                    COUNT(*) AS CantidadVandalismo
                FROM TICKET t
                INNER JOIN AULA a ON a.ID = t.PcAulaID
                LEFT JOIN LABORATORIO l ON l.AulaID = a.ID
                WHERE t.Fallo IN ('falta_mouse', 'falta_teclado')" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "t.FechaCreacion"
                ) . "
                GROUP BY
                    t.PcNumPc,
                    t.PcAulaID,
                    a.Numero,
                    l.AulaID
                ORDER BY CantidadVandalismo DESC
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        $fila = $consulta->fetch(PDO::FETCH_ASSOC);

        return $fila === false ? null : $fila;
    }

    public function listarPcsSospechosasDeVandalismo(
        int $umbral = 3,
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): array {

        $sql = "SELECT
                    t.PcNumPc,
                    t.PcAulaID,
                    a.Numero AS AulaNumero,
                    CASE
                        WHEN l.AulaID IS NOT NULL THEN 'laboratorio'
                        ELSE 'taller'
                    END AS AulaTipo,
                    COUNT(*) AS CantidadVandalismo
                FROM TICKET t
                INNER JOIN AULA a ON a.ID = t.PcAulaID
                LEFT JOIN LABORATORIO l ON l.AulaID = a.ID
                WHERE t.Fallo IN ('falta_mouse', 'falta_teclado')" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "t.FechaCreacion"
                ) . "
                GROUP BY
                    t.PcNumPc,
                    t.PcAulaID,
                    a.Numero,
                    l.AulaID
                HAVING COUNT(*) >= :umbral
                ORDER BY CantidadVandalismo DESC";

        $parametros = array_merge(
            ["umbral" => $umbral],
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerModeloMouseMasDanado(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): ?array {

        $sql = "SELECT
                    p.Modelo,
                    COUNT(*) AS CantidadReportes
                FROM TICKET t
                INNER JOIN PERIFERICO p
                    ON p.PcNumPc = t.PcNumPc
                    AND p.PcAulaID = t.PcAulaID
                    AND p.Tipo = 'mouse'
                WHERE t.Fallo IN ('falta_mouse', 'mouse_no_funciona')" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "t.FechaCreacion"
                ) . "
                GROUP BY p.Modelo
                ORDER BY CantidadReportes DESC
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        $fila = $consulta->fetch(PDO::FETCH_ASSOC);

        return $fila === false ? null : $fila;
    }

    public function obtenerModeloTecladoMasDanado(
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): ?array {

        $sql = "SELECT
                    p.Modelo,
                    COUNT(*) AS CantidadReportes
                FROM TICKET t
                INNER JOIN PERIFERICO p
                    ON p.PcNumPc = t.PcNumPc
                    AND p.PcAulaID = t.PcAulaID
                    AND p.Tipo = 'teclado'
                WHERE t.Fallo IN ('falta_teclado', 'teclado_no_funciona')" .
                $this->filtroFecha(
                    $fechaInicio,
                    $fechaFin,
                    "t.FechaCreacion"
                ) . "
                GROUP BY p.Modelo
                ORDER BY CantidadReportes DESC
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(
            $this->parametrosFecha($fechaInicio, $fechaFin)
        );

        $fila = $consulta->fetch(PDO::FETCH_ASSOC);

        return $fila === false ? null : $fila;
    }
}
