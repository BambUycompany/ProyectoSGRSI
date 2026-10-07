const API_METRICAS = "../index.php?ruta=metricas";

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();
    if (!texto.trim()) {
        throw new Error(`La API respondió sin cuerpo (HTTP ${respuesta.status}).`);
    }
    let json;
    try {
        json = JSON.parse(texto);
    } catch {
        throw new Error(`HTTP ${respuesta.status}: La API no devolvió JSON.`);
    }
    if (!respuesta.ok) {
        throw new Error(`HTTP ${respuesta.status}: ${json.mensaje ?? "La solicitud no se pudo completar."}`);
    }
    return json.datos;
}

async function obtenerMetricas(periodo) {
    const respuesta = await fetch(`${API_METRICAS}?recurso=dashboard&periodo=${encodeURIComponent(periodo)}`);
    return await leerRespuestaAPI(respuesta);
}

function pintarMetricas(metricas) {
    document.getElementById("valorTotalRegistros").textContent = metricas.totalRegistros;
    document.getElementById("valorTotalTickets").textContent = metricas.totalTickets;
    document.getElementById("valorTotalFinalizados").textContent = metricas.totalFinalizados;
    document.getElementById("valorBajoRendimiento").textContent = metricas.totalBajoRendimiento;
    document.getElementById("valorTiempoPromedio").textContent =
        metricas.tiempoPromedioResolucion !== null ? metricas.tiempoPromedioResolucion + "h" : "—";

    const pcMasReportada = document.getElementById("pcMasReportada");
    pcMasReportada.textContent = metricas.pcMasReportada
        ? `${metricas.pcMasReportada.PcNumPc} (${metricas.pcMasReportada.AulaTipo} ${metricas.pcMasReportada.AulaNumero}) — ${metricas.pcMasReportada.CantidadReportes} reportes`
        : "Sin datos";

    const pcMasVandalizada = document.getElementById("pcMasVandalizada");
    pcMasVandalizada.textContent = metricas.pcMasVandalizada
        ? `${metricas.pcMasVandalizada.PcNumPc} (${metricas.pcMasVandalizada.AulaTipo} ${metricas.pcMasVandalizada.AulaNumero}) — ${metricas.pcMasVandalizada.CantidadVandalismo} reportes`
        : "Sin reportes";
}

async function cargarMetricas(periodo = "todo") {
    try {
        const resultado = await obtenerMetricas(periodo);
        pintarMetricas(resultado.metricas);
    } catch (error) {
        window.alert("No se pudieron cargar las métricas: " + error.message);
    }
}

for (const boton of document.querySelectorAll(".filtroPeriodo button")) {
    boton.addEventListener("click", () => cargarMetricas(boton.dataset.periodo));
}

cargarMetricas();