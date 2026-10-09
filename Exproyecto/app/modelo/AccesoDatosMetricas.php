<?php
class AccesoDatosMetricas{
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    private function filtroFecha(?string $fechaInicio, ?string $fechaFin, string $columna): string {
        if ($fechaInicio === null || $fechaFin === null) {
            return "";
        }
        return " AND DATE($columna) BETWEEN :fechaInicio AND :fechaFin ";
    }

    private function parametrosFecha(?string $fechaInicio, ?string $fechaFin): array {
        if ($fechaInicio === null || $fechaFin === null) {
            return [];
        }
        return ["fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin];
    }
public function contarTicketsEmitidos(?string $fechaInicio = null, ?string $fechaFin = null): int {
        $sql = "SELECT COUNT(*) FROM TICKET WHERE 1=1" . $this->filtroFecha($fechaInicio, $fechaFin, "FechaCreacion");
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
        return (int) $consulta->fetchColumn();
    }

    public function contarRegistrosEmitidos(?string $fechaInicio = null, ?string $fechaFin = null): int {
        $sql = "SELECT COUNT(*) FROM PLANILLA WHERE 1=1" . $this->filtroFecha($fechaInicio, $fechaFin, "Fecha");
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
        return (int) $consulta->fetchColumn();
    }

    public function contarTicketsFinalizados(?string $fechaInicio = null, ?string $fechaFin = null): int {
        $sql = "SELECT COUNT(*) FROM TICKET WHERE Estado = 'finalizado'" . $this->filtroFecha($fechaInicio, $fechaFin, "FechaCreacion");
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
        return (int) $consulta->fetchColumn();
    }

    public function contarPorBajoRendimiento(?string $fechaInicio = null, ?string $fechaFin = null): int {
        $sql = "SELECT COUNT(*) FROM TICKET WHERE Fallo = 'bajo_rendimiento'" . $this->filtroFecha($fechaInicio, $fechaFin, "FechaCreacion");
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
        return (int) $consulta->fetchColumn();
    }

    public function obtenerPcMasReportada(?string $fechaInicio = null, ?string $fechaFin = null) {
        $sql = "SELECT TICKET.PcNumPc, TICKET.PcAulaID, AULA.Numero AS AulaNumero,
                    CASE WHEN EXISTS (SELECT 1 FROM LABORATORIO WHERE LABORATORIO.AulaID = AULA.ID) THEN 'laboratorio' ELSE 'taller' END AS AulaTipo,
                    COUNT(*) AS CantidadReportes
                FROM TICKET
                JOIN AULA ON AULA.ID = TICKET.PcAulaID
                WHERE 1=1" . $this->filtroFecha($fechaInicio, $fechaFin, "TICKET.FechaCreacion") . "
                GROUP BY TICKET.PcNumPc, TICKET.PcAulaID, AULA.Numero, AULA.ID
                ORDER BY CantidadReportes DESC
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }

    public function obtenerPcMasVandalizada(?string $fechaInicio = null, ?string $fechaFin = null) {
        $sql = "SELECT TICKET.PcNumPc, TICKET.PcAulaID, AULA.Numero AS AulaNumero,
                    CASE WHEN EXISTS (SELECT 1 FROM LABORATORIO WHERE LABORATORIO.AulaID = AULA.ID) THEN 'laboratorio' ELSE 'taller' END AS AulaTipo,
                    COUNT(*) AS CantidadVandalismo
                FROM TICKET
                JOIN AULA ON AULA.ID = TICKET.PcAulaID
                WHERE Fallo IN ('falta_mouse', 'falta_teclado')" . $this->filtroFecha($fechaInicio, $fechaFin, "TICKET.FechaCreacion") . "
                GROUP BY TICKET.PcNumPc, TICKET.PcAulaID, AULA.Numero, AULA.ID
                ORDER BY CantidadVandalismo DESC
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }

    public function listarPcsSospechosasDeVandalismo(int $umbral = 3, ?string $fechaInicio = null, ?string $fechaFin = null) {
        $sql = "SELECT TICKET.PcNumPc, TICKET.PcAulaID, AULA.Numero AS AulaNumero,
                    CASE WHEN EXISTS (SELECT 1 FROM LABORATORIO WHERE LABORATORIO.AulaID = AULA.ID) THEN 'laboratorio' ELSE 'taller' END AS AulaTipo,
                    COUNT(*) AS CantidadVandalismo
                FROM TICKET
                JOIN AULA ON AULA.ID = TICKET.PcAulaID
                WHERE Fallo IN ('falta_mouse', 'falta_teclado')" . $this->filtroFecha($fechaInicio, $fechaFin, "TICKET.FechaCreacion") . "
                GROUP BY TICKET.PcNumPc, TICKET.PcAulaID, AULA.Numero, AULA.ID
                HAVING COUNT(*) >= :umbral
                ORDER BY CantidadVandalismo DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(array_merge(["umbral" => $umbral], $this->parametrosFecha($fechaInicio, $fechaFin)));
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calcularTiempoPromedioResolucion(?string $fechaInicio = null, ?string $fechaFin = null) {
        $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, FechaCreacion, FechaFinalizacion)) AS PromedioHoras
                FROM TICKET
                WHERE Estado = 'finalizado' AND FechaFinalizacion IS NOT NULL" . $this->filtroFecha($fechaInicio, $fechaFin, "FechaCreacion");

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
        $resultado = $consulta->fetchColumn();
        return $resultado === null ? null : round((float) $resultado, 1);
    }

    public function obtenerAulaConMasIncidencias(?string $fechaInicio = null, ?string $fechaFin = null) {
        $sql = "SELECT AULA.ID, AULA.Numero,
                    CASE WHEN EXISTS (SELECT 1 FROM LABORATORIO WHERE LABORATORIO.AulaID = AULA.ID) THEN 'laboratorio' ELSE 'taller' END AS Tipo,
                    COUNT(TICKET.ID) AS CantidadTickets
                FROM AULA
                JOIN TICKET ON TICKET.PcAulaID = AULA.ID
                WHERE 1=1" . $this->filtroFecha($fechaInicio, $fechaFin, "TICKET.FechaCreacion") . "
                GROUP BY AULA.ID, AULA.Numero
                ORDER BY CantidadTickets DESC
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }

    public function obtenerModeloMouseMasDanado(?string $fechaInicio = null, ?string $fechaFin = null) {
    $sql = "SELECT PERIFERICO.Modelo, COUNT(*) AS CantidadReportes
            FROM TICKET
            JOIN PERIFERICO ON PERIFERICO.PcNumPc = TICKET.PcNumPc
                           AND PERIFERICO.PcAulaID = TICKET.PcAulaID
                           AND PERIFERICO.Tipo = 'mouse'
            WHERE TICKET.Fallo IN ('falta_mouse', 'mouse_no_funciona')" . $this->filtroFecha($fechaInicio, $fechaFin, "TICKET.FechaCreacion") . "
            GROUP BY PERIFERICO.Modelo
            ORDER BY CantidadReportes DESC
            LIMIT 1";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
    $fila = $consulta->fetch(PDO::FETCH_ASSOC);
    return $fila === false ? null : $fila;
    }

    public function obtenerModeloTecladoMasDanado(?string $fechaInicio = null, ?string $fechaFin = null) {
    $sql = "SELECT PERIFERICO.Modelo, COUNT(*) AS CantidadReportes
            FROM TICKET
            JOIN PERIFERICO ON PERIFERICO.PcNumPc = TICKET.PcNumPc
                           AND PERIFERICO.PcAulaID = TICKET.PcAulaID
                           AND PERIFERICO.Tipo = 'teclado'
            WHERE TICKET.Fallo IN ('falta_teclado', 'teclado_no_funciona')" . $this->filtroFecha($fechaInicio, $fechaFin, "TICKET.FechaCreacion") . "
            GROUP BY PERIFERICO.Modelo
            ORDER BY CantidadReportes DESC
            LIMIT 1";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute($this->parametrosFecha($fechaInicio, $fechaFin));
    $fila = $consulta->fetch(PDO::FETCH_ASSOC);
    return $fila === false ? null : $fila;
    }

}