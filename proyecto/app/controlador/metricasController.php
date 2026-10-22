<?php
require_once __DIR__ . "/../nucleo/Conexion.php";
require_once RUTA_MODELO . "/MetricasDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class MetricasController {

    private MetricasDAO $dao;

    public function __construct() {

        $conexion = Conexion::conectar();

        $this->dao = new MetricasDAO($conexion);
    }

    public function gestionar(
        string $metodo,
        string $recurso
    ): void {

        if (empty($_SESSION["cedula"])) {
            RespuestaJson::error(
                "Tu sesión no está iniciada o ha vencido.",
                401
            );
        }

        if (empty($_SESSION["administrador"])) {
            RespuestaJson::error(
                "No tenés permiso para consultar las métricas.",
                403
            );
        }

        if ($metodo !== "GET") {
            RespuestaJson::error(
                "Método no permitido.",
                405
            );
        }

        if ($recurso !== "dashboard") {
            RespuestaJson::error(
                "Recurso de métricas no encontrado.",
                404
            );
        }

        try {
            $this->obtenerDashboardMetricas();

        } catch (Throwable $error) {

            error_log(
                "Error en MetricasController: " .
                $error->getMessage()
            );

            RespuestaJson::error(
                "Ocurrió un error al obtener las métricas del sistema.",
                500
            );
        }
    }

    private function obtenerDashboardMetricas(): void {

        $periodo = $_GET["periodo"] ?? "todo";

        $periodosPermitidos = [
            "dia",
            "semana",
            "mes",
            "todo"
        ];

        if (!in_array($periodo, $periodosPermitidos, true)) {
            RespuestaJson::error(
                "El período seleccionado no es válido.",
                400
            );
        }

        $fechaInicio = null;
        $fechaFin = null;

        switch ($periodo) {

            case "dia":
                $fechaInicio = date("Y-m-d");
                $fechaFin = date("Y-m-d");
                break;

            case "semana":
                $fechaInicio = date(
                    "Y-m-d",
                    strtotime("monday this week")
                );
                $fechaFin = date("Y-m-d");
                break;

            case "mes":
                $fechaInicio = date("Y-m-01");
                $fechaFin = date("Y-m-d");
                break;

            case "todo":
                break;
        }

        $metricas = [

            "periodoAplicado" => $periodo,

            "totalRegistros" =>
                $this->dao->contarRegistrosEmitidos(
                    $fechaInicio,
                    $fechaFin
                ),

            "totalTickets" =>
                $this->dao->contarTicketsEmitidos(
                    $fechaInicio,
                    $fechaFin
                ),

            "totalFinalizados" =>
                $this->dao->contarTicketsPorEstado(
                    "finalizado",
                    $fechaInicio,
                    $fechaFin
                ),

            "totalPendientes" =>
                $this->dao->contarTicketsPorEstado(
                    "pendiente",
                    $fechaInicio,
                    $fechaFin
                ),

            "totalEnProceso" =>
                $this->dao->contarTicketsPorEstado(
                    "en proceso",
                    $fechaInicio,
                    $fechaFin
                ),

            "totalBajoRendimiento" =>
                $this->dao->contarPorBajoRendimiento(
                    $fechaInicio,
                    $fechaFin
                ),

            "totalPortatilesPrestadas" =>
                $this->dao->contarPortatilesPrestadas(),

            "topPcsReportadas" =>
                $this->dao->listarTopPcsReportadas(
                    $fechaInicio,
                    $fechaFin
                ),

            "topAulasConIncidencias" =>
                $this->dao->listarTopAulasConIncidencias(
                    $fechaInicio,
                    $fechaFin
                ),

            "fallasFrecuentes" =>
                $this->dao->listarFallasFrecuentes(
                    $fechaInicio,
                    $fechaFin
                ),

            "pcMasVandalizada" =>
                $this->dao->obtenerPcMasVandalizada(
                    $fechaInicio,
                    $fechaFin
                ),

            "pcsSospechosas" =>
                $this->dao->listarPcsSospechosasDeVandalismo(
                    3,
                    $fechaInicio,
                    $fechaFin
                ),

            "modeloMouseMasDanado" =>
                $this->dao->obtenerModeloMouseMasDanado(
                    $fechaInicio,
                    $fechaFin
                ),

            "modeloTecladoMasDanado" =>
                $this->dao->obtenerModeloTecladoMasDanado(
                    $fechaInicio,
                    $fechaFin
                )
        ];

        RespuestaJson::exito([
            "metricas" => $metricas
        ]);
    }
}
