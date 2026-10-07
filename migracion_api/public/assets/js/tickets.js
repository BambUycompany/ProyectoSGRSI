const API_TICKETS = "../index.php?ruta=tickets";

const cuerpoTablaTicketsAgrupados = document.getElementById("cuerpoTablaTicketsAgrupados");

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

async function obtenerTicketsAgrupados() {
    const respuesta = await fetch(API_TICKETS);
    return await leerRespuestaAPI(respuesta);
}

async function obtenerTicketsPorPcYAula(numPc, aulaId) {
    const respuesta = await fetch(`${API_TICKETS}?numPc=${encodeURIComponent(numPc)}&aulaId=${encodeURIComponent(aulaId)}`);
    return await leerRespuestaAPI(respuesta);
}


async function actualizarTicket(ticketId, cambios) {
    const respuesta = await fetch(API_TICKETS, {
        method: "PATCH",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
        },
        body: JSON.stringify({ ticketId, ...cambios })
    });
    return await leerRespuestaAPI(respuesta);
}

function agregarFilaTicketAgrupado(grupo) {
    const fila = document.createElement("tr");

    const campoPc = document.createElement("td");
    campoPc.textContent = grupo.PcNumPc;

    const campoSala = document.createElement("td");
    campoSala.textContent = `${grupo.AulaTipo} ${grupo.AulaNumero}`;

    const campoCantidad = document.createElement("td");
    const linkDetalle = document.createElement("a");
    linkDetalle.href = "#";
    linkDetalle.textContent = grupo.CantidadReportes;
    linkDetalle.addEventListener("click", (evento) => {
        evento.preventDefault();
        verDetalleTickets(grupo.PcNumPc, grupo.PcAulaID);
    });
    campoCantidad.appendChild(linkDetalle);

    fila.appendChild(campoPc);
    fila.appendChild(campoSala);
    fila.appendChild(campoCantidad);

    cuerpoTablaTicketsAgrupados.appendChild(fila);
}

async function actualizarTablaAgrupados() {
    cuerpoTablaTicketsAgrupados.replaceChildren();
    try {
        const grupos = await obtenerTicketsAgrupados();
        for (const grupo of grupos) {
            agregarFilaTicketAgrupado(grupo);
        }
    } catch (error) {
        window.alert("No se pudieron cargar los tickets: " + error.message);
    }
}

async function verDetalleTickets(numPc, aulaId) {
    try {
        const tickets = await obtenerTicketsPorPcYAula(numPc, aulaId);
        console.log("Tickets de " + numPc + ":", tickets);
        // Acá se pintaría el detalle en pantalla, según el HTML que tengas armado para esto
    } catch (error) {
        window.alert(error.message);
    }
}

actualizarTablaAgrupados();