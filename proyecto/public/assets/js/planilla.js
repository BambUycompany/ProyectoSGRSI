
const API_PLANILLA = "../index.php?ruta=planilla";

const formularioRegistro = document.getElementById(
    "formularioRegistroPlanilla"
);

const cuerpoTablaRegistros = document.getElementById(
    "cuerpoTablaRegistros"
);

async function leerRespuestaAPI(respuesta) {

    const texto = await respuesta.text();

    if (!texto.trim()) {
        throw new Error(
            `La API respondió sin cuerpo (HTTP ${respuesta.status}).`
        );
    }

    let json;

    try {
        json = JSON.parse(texto);
    } catch {
        console.error("Respuesta inesperada:", texto);

        throw new Error(
            `HTTP ${respuesta.status}: La API no devolvió JSON.`
        );
    }

    if (!respuesta.ok) {
        throw new Error(
            `HTTP ${respuesta.status}: ` +
            (json.mensaje ?? "Error en la solicitud.")
        );
    }

    return json.datos;
}

async function obtenerPlanillas(periodo = "todo") {

    const respuesta = await fetch(
        `${API_PLANILLA}&periodo=${encodeURIComponent(periodo)}`
    );

    return await leerRespuestaAPI(respuesta);
}

async function registrarPlanilla(datosPlanilla) {

    const respuesta = await fetch(API_PLANILLA, {

        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token":
                sessionStorage.getItem("csrfToken") ?? ""
        },

        body: JSON.stringify(datosPlanilla)
    });

    return await leerRespuestaAPI(respuesta);
}

function agregarFilaRegistro(planilla) {

    if (!cuerpoTablaRegistros) return;

    const fila = document.createElement("tr");

    const valores = [
        planilla.Fecha,
        planilla.HoraEntrada,
        `${planilla.AulaTipo} ${planilla.AulaNumero}`
    ];

    for (const valor of valores) {
        const celda = document.createElement("td");
        celda.textContent = valor ?? "-";
        fila.appendChild(celda);
    }

    cuerpoTablaRegistros.appendChild(fila);
}

async function actualizarTablaRegistros() {

    if (!cuerpoTablaRegistros) return;

    try {
        const resultado = await obtenerPlanillas();

        cuerpoTablaRegistros.replaceChildren();

        const planillas = resultado.planillas ?? [];

        for (const planilla of planillas) {
            agregarFilaRegistro(planilla);
        }

    } catch (error) {
        window.alert(
            "No se pudieron cargar los registros: " +
            error.message
        );
    }
}

async function gestionarRegistroPlanilla(eventoFormulario) {

    eventoFormulario.preventDefault();

    if (!formularioRegistro.reportValidity()) {
        return;
    }

    try {

        const datosFormulario = new FormData(
            formularioRegistro
        );

        const datosPlanilla = {
            tipo: datosFormulario.get("tipo"),
            numero: datosFormulario.get("numero"),
            fecha: datosFormulario.get("fecha"),
            horaEntrada: datosFormulario.get("horaEntrada"),
            horaSalida: datosFormulario.get("horaSalida"),
            nombreSolicitante:
                datosFormulario.get("nombreSolicitante"),

            Asignatura:
                datosFormulario.get("Asignatura") ?? "",

            grupo:
                datosFormulario.get("grupo") ?? "",

            turno:
                datosFormulario.get("turno") ?? "",

            tickets: obtenerTicketsDelFormulario()
        };

        const resultado = await registrarPlanilla(
            datosPlanilla
        );

        window.alert(
            "Planilla registrada correctamente.\n" +
            "ID: " + resultado.planillaId + "\n" +
            "Tickets creados: " + resultado.cantidadTickets
        );

        formularioRegistro.reset();
        limpiarTickets();

        actualizarNumeros();

        await actualizarTablaRegistros();

    } catch (error) {

        window.alert(error.message);
    }
}

if (formularioRegistro) {
    formularioRegistro.addEventListener(
        "submit",
        gestionarRegistroPlanilla
    );
}

if (cuerpoTablaRegistros) {
    actualizarTablaRegistros();
}
