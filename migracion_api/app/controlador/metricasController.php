<?php
// MetricasController.php

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/MetricasDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class MetricasController {
    private MetricasDAO $dao;

    public function __construct() {
        $conector = new ConectorPDO($_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"], $_ENV["DB_USUARIO"], $_ENV["DB_CLAVE"], $_ENV["DB_NOMBRE"]);
        $conexion = $conector->establecerConexion();
        
        if ($conexion === null) {
            RespuestaJson::error("Error de conexión a la base de datos", 500);
        }
        $this->dao = new MetricasDAO($conexion);
    }

    public function gestionar(string $metodo, string $recurso): void {
        if ($metodo !== "GET") {
            RespuestaJson::error("Método no permitido", 405);
        }

        if ($recurso === "dashboard") {
            $this->obtenerDashboardMetricas();
        } else {
            RespuestaJson::error("Recurso de métricas no encontrado", 404);
        }
    }

    private function obtenerDashboardMetricas(): void {
        $periodo = $_GET["periodo"] ?? "todo";
        $fechaInicio = null;
        $fechaFin = null;

        switch ($periodo) {
            case "dia":
                $fechaInicio = date("Y-m-d");
                $fechaFin = date("Y-m-d");
                break;
            case "semana":
                $fechaInicio = date("Y-m-d", strtotime("monday this week"));
                $fechaFin = date("Y-m-d");
                break;
            case "mes":
                $fechaInicio = date("Y-m-01");
                $fechaFin = date("Y-m-d");
                break;
            default:
                $periodo = "todo";
                break;
        }

        $metricas = [
            "periodoAplicado" => $periodo,
            "totalTickets" => $this->dao->contarTicketsEmitidos($fechaInicio, $fechaFin),
            "totalRegistros" => $this->dao->contarRegistrosEmitidos($fechaInicio, $fechaFin),
            "totalFinalizados" => $this->dao->contarTicketsFinalizados($fechaInicio, $fechaFin),
            "totalBajoRendimiento" => $this->dao->contarPorBajoRendimiento($fechaInicio, $fechaFin),
            "pcMasReportada" => $this->dao->obtenerPcMasReportada($fechaInicio, $fechaFin),
            "pcMasVandalizada" => $this->dao->obtenerPcMasVandalizada($fechaInicio, $fechaFin),
            "pcsSospechosas" => $this->dao->listarPcsSospechosasDeVandalismo(3, $fechaInicio, $fechaFin),
            "tiempoPromedioResolucion" => $this->dao->calcularTiempoPromedioResolucion($fechaInicio, $fechaFin),
            "aulaConMasIncidencias" => $this->dao->obtenerAulaConMasIncidencias($fechaInicio, $fechaFin),
            "modeloMouseMasDanado" => $this->dao->obtenerModeloMouseMasDanado($fechaInicio, $fechaFin),
            "modeloTecladoMasDanado" => $this->dao->obtenerModeloTecladoMasDanado($fechaInicio, $fechaFin)
        ];

        RespuestaJson::exito(["metricas" => $metricas]);
    }
}
?>