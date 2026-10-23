

const API_PLANILLA = "../index.php?recurso=planilla";

const formularioRegistro = document.getElementById(
    "formularioRegistroPlanilla"
);

const cuerpoTablaRegistros = document.getElementById(
    "cuerpoTablaRegistros"
);

async function completarNombreSolicitante() {
    const campo = document.getElementById("nombreSolicitante");

    if (!campo) return;

    const nombre = sessionStorage.getItem("nombre") ?? "";
    const apellido = sessionStorage.getItem("apellido") ?? "";

    campo.value = `${nombre} ${apellido}`.trim();
    campo.readOnly = true;
}

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

async function agregarFilaRegistro(planilla) {
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
            await agregarFilaRegistro(planilla);
        }

    } catch (error) {
        await mostrarToast(
            "No se pudieron cargar los registros: " +
            error.message,
            "error"
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

            tickets: await obtenerTicketsDelFormulario()
        };

        const resultado = await registrarPlanilla(
            datosPlanilla
        );

        await mostrarToast(
            "Planilla registrada correctamente.\n" +
            "ID: " + resultado.planillaId + "\n" +
            "Tickets creados: " + resultado.cantidadTickets,
            "exito"
        );

        formularioRegistro.reset();

        await limpiarTickets();
        await actualizarNumeros();
        await actualizarTablaRegistros();

        await completarNombreSolicitante();

    } catch (error) {
        await mostrarToast(
            error.message,
            "error"
        );
    }
}

async function configurarCamposDocente() {
    const camposDocente = document.getElementById("camposDocente");

    if (!camposDocente) return;

    const rolActivoRegistro = sessionStorage.getItem("rolActivo");
    const mostrarCampos = rolActivoRegistro === "solicitante";

    camposDocente.hidden = !mostrarCampos;

    for (
        const campo of camposDocente.querySelectorAll(
            "input, select, textarea"
        )
    ) {
        campo.disabled = !mostrarCampos;

        if (!mostrarCampos) {
            campo.required = false;
        }
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

completarNombreSolicitante();
configurarCamposDocente();

