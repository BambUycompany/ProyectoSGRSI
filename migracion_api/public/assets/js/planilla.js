const API_PLANILLA = "../index.php?ruta=planilla";

const formularioRegistro = document.getElementById("formularioRegistroPlanilla");
const cuerpoTablaRegistros = document.getElementById("cuerpoTablaRegistros");

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

async function obtenerPlanillas() {
    const respuesta = await fetch(API_PLANILLA);
    return await leerRespuestaAPI(respuesta);
}

async function registrarPlanilla(datosPlanilla) {
    const respuesta = await fetch(API_PLANILLA, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
        },
        body: JSON.stringify(datosPlanilla)
    });
    return await leerRespuestaAPI(respuesta);
}

function agregarFilaRegistro(planilla) {
    const fila = document.createElement("tr");

    const campoFecha = document.createElement("td");
    campoFecha.textContent = planilla.Fecha;

    const campoHora = document.createElement("td");
    campoHora.textContent = planilla.HoraEntrada;

    const campoSala = document.createElement("td");
    campoSala.textContent = `${planilla.AulaTipo} ${planilla.AulaNumero}`;

    fila.appendChild(campoFecha);
    fila.appendChild(campoHora);
    fila.appendChild(campoSala);

    cuerpoTablaRegistros.appendChild(fila);
}

async function actualizarTablaRegistros() {
    cuerpoTablaRegistros.replaceChildren();
    try {
        const resultado = await obtenerPlanillas();
        const planillas = resultado.planillas ?? resultado;
        for (const planilla of planillas) {
            agregarFilaRegistro(planilla);
        }
    } catch (error) {
        window.alert("No se pudieron cargar los registros: " + error.message);
    }
}

function obtenerTicketsDelFormulario() {
    const filas = document.querySelectorAll("#tablaTicketsPendientes tr[data-ticket]");
    const tickets = [];
    for (const fila of filas) {
        tickets.push(JSON.parse(fila.dataset.ticket));
    }
    return tickets;
}

async function gestionarRegistroPlanilla(eventoFormulario) {
    eventoFormulario.preventDefault();

    try {
        const datosFormulario = new FormData(formularioRegistro);

        const datosPlanilla = {
            tipo: datosFormulario.get("tipo"),
            numero: datosFormulario.get("numero"),
            fecha: datosFormulario.get("fecha"),
            horaEntrada: datosFormulario.get("horaEntrada"),
            horaSalida: datosFormulario.get("horaSalida"),
            nombreSolicitante: datosFormulario.get("nombreSolicitante"),
            Asignatura: datosFormulario.get("Asignatura"),
            grupo: datosFormulario.get("grupo"),
            turno: datosFormulario.get("turno"),
            tickets: obtenerTicketsDelFormulario()
        };

        await registrarPlanilla(datosPlanilla);

        formularioRegistro.reset();
        window.alert("Registro guardado correctamente.");
        await actualizarTablaRegistros();
    } catch (error) {
        window.alert(error.message);
    }
}

formularioRegistro.addEventListener("submit", gestionarRegistroPlanilla);

actualizarTablaRegistros();