
(function () {
    const parametros = new URLSearchParams(window.location.search);
    const planillaId = parametros.get("id");

    const tituloRegistro = document.getElementById("tituloRegistro");
    const mensajeRegistro = document.getElementById("mensajeRegistro");
    const mensajeSinTickets = document.getElementById("mensajeSinTickets");
    const cuerpoTablaTickets = document.getElementById("cuerpoTablaTickets");

    const datoFecha = document.getElementById("datoFecha");
    const datoHorario = document.getElementById("datoHorario");
    const datoAula = document.getElementById("datoAula");
    const datoSolicitante = document.getElementById("datoSolicitante");
    const datoAsignatura = document.getElementById("datoAsignatura");
    const datoGrupo = document.getElementById("datoGrupo");
    const datoTurno = document.getElementById("datoTurno");

    const dialogDiagnostico = document.getElementById("dialogDiagnostico");
    const textoDiagnostico = document.getElementById("textoDiagnostico");
    const btnCerrarDiagnostico = document.getElementById("btnCerrarDiagnostico");

    function mostrarMensaje(mensaje, tipo = "error") {
        mensajeRegistro.textContent = mensaje;
        mensajeRegistro.className = "mensajeRegistro " + tipo;
        mensajeRegistro.hidden = false;
    }

    function manejarError(error) {
        if (error.status === 401 || error.status === 403) {
            sessionStorage.setItem("errorAcceso", error.message);
            window.location.replace("./login.html");
            return;
        }

        mostrarMensaje(error.message);
    }

    async function consultarDetalle() {
        
        const parametrosAPI = new URLSearchParams({
            ruta: "planilla",
            id: planillaId
        });

        if (sessionStorage.getItem("rolActivo") === "solicitante") {
            parametrosAPI.set("vista", "propios");
        }

        const respuesta = await fetch(
            "../index.php?" + parametrosAPI.toString(),
            {
                method: "GET",
                cache: "no-store"
            }
        );

        let resultado;

        try {
            resultado = await respuesta.json();
        } catch {
            throw new Error(
                "El servidor no devolvió una respuesta válida."
            );
        }

        if (!respuesta.ok) {
            const error = new Error(
                resultado.mensaje ?? "No se pudo consultar la planilla."
            );

            error.status = respuesta.status;
            throw error;
        }

        if (
            !resultado.datos?.planilla ||
            !Array.isArray(resultado.datos?.tickets)
        ) {
            throw new Error(
                "Los datos de la planilla no tienen el formato esperado."
            );
        }

        return resultado.datos;
    }

    function mostrarDato(elemento, valor) {
        elemento.textContent =
            valor === null || valor === undefined || valor === ""
                ? "-"
                : valor;
    }

    function formatearFecha(fecha) {
        if (!fecha) {
            return "-";
        }

        const partes = String(fecha).split("-");

        if (partes.length !== 3) {
            return String(fecha);
        }

        return partes[2] + "/" + partes[1] + "/" + partes[0];
    }

    function formatearHora(hora) {
        if (!hora) {
            return "-";
        }

        return String(hora).slice(0, 5);
    }

    function formatearFechaHora(valor) {
        if (!valor) {
            return "-";
        }

        const partes = String(valor).split(" ");

        return formatearFecha(partes[0]) +
            (partes[1] ? " " + partes[1].slice(0, 5) : "");
    }

    function crearCelda(valor) {
        const celda = document.createElement("td");
        celda.textContent = valor ?? "-";
        return celda;
    }

    function abrirDiagnostico(diagnostico) {
        textoDiagnostico.textContent =
            diagnostico || "No se registró un diagnóstico.";

        dialogDiagnostico.showModal();
    }

    function mostrarPlanilla(planilla) {
        tituloRegistro.textContent = "Registro N.º " + planilla.ID;

        const aulaTipo = String(planilla.AulaTipo ?? "");
        const aulaNumero = String(planilla.AulaNumero ?? "");

        const nombreAula = (
            aulaTipo.charAt(0).toUpperCase() +
            aulaTipo.slice(1) +
            " " +
            aulaNumero
        ).trim();

        mostrarDato(datoFecha, formatearFecha(planilla.Fecha));

        mostrarDato(
            datoHorario,
            formatearHora(planilla.HoraEntrada) +
            " a " +
            formatearHora(planilla.HoraSalida)
        );

        mostrarDato(datoAula, nombreAula);
        mostrarDato(datoSolicitante, planilla.NombreSolicitante);
        mostrarDato(datoAsignatura, planilla.Asignatura);
        mostrarDato(datoGrupo, planilla.Grupo);
        mostrarDato(datoTurno, planilla.Turno);
    }

    function agregarFilaTicket(ticket, planilla) {
        const fila = document.createElement("tr");

        fila.appendChild(crearCelda(ticket.ID));
        fila.appendChild(crearCelda(ticket.PcNumPc));
        fila.appendChild(crearCelda(ticket.Fallo));
        fila.appendChild(crearCelda(ticket.Descripcion));
        fila.appendChild(crearCelda(ticket.Estado));
        fila.appendChild(crearCelda(ticket.Prioridad));
        fila.appendChild(
            crearCelda(formatearFechaHora(ticket.FechaCreacion))
        );

        const celdaDiagnostico = document.createElement("td");

        const finalizado =
            String(ticket.Estado ?? "").toLowerCase() === "finalizado";

        if (finalizado) {
            const boton = document.createElement("button");

            boton.type = "button";
            boton.className = "botones btnDiagnostico";
            boton.textContent = "Ver diagnóstico";

            boton.addEventListener("click", function () {
                abrirDiagnostico(ticket.Incidente);
            });

            celdaDiagnostico.appendChild(boton);
        } else {
            celdaDiagnostico.textContent = "-";
        }

        fila.appendChild(celdaDiagnostico);

        const celdaAcciones = document.createElement("td");
        const enlace = document.createElement("a");

        const parametrosDetalle = new URLSearchParams({
            pc: String(ticket.PcNumPc),
            aulaId: String(ticket.PcAulaID ?? planilla.AulaID)
        });

        enlace.href =
            "./detalle_tickets.html?" + parametrosDetalle.toString();

        enlace.textContent = "Ver tickets de la PC";
        enlace.className = "botones";

        celdaAcciones.appendChild(enlace);
        fila.appendChild(celdaAcciones);

        cuerpoTablaTickets.appendChild(fila);
    }

    
    const enlaceVolver = document.getElementById("enlaceVolverRegistros");

    if (enlaceVolver && sessionStorage.getItem("rolActivo") === "solicitante") {
        enlaceVolver.href = "./solicitante.html";
        enlaceVolver.textContent = "← Volver";
    }

    if (sessionStorage.getItem("rolActivo") === "solicitante") {
        document.querySelectorAll(
            'a[href*="detalle_tickets.html"]'
        ).forEach(enlace => enlace.remove());
    }


    async function cargarDetalleRegistro() {
        if (!/^[1-9]\d*$/.test(planillaId ?? "")) {
            mostrarMensaje("El identificador del registro no es válido.");
            return;
        }

        try {
            const datos = await consultarDetalle();

            mostrarPlanilla(datos.planilla);

            cuerpoTablaTickets.replaceChildren();

            if (datos.tickets.length === 0) {
                mensajeSinTickets.hidden = false;
                return;
            }

            mensajeSinTickets.hidden = true;

            for (const ticket of datos.tickets) {
                agregarFilaTicket(ticket, datos.planilla);
            }

        } catch (error) {
            manejarError(error);
        }
    }

    btnCerrarDiagnostico.addEventListener("click", function () {
        dialogDiagnostico.close();
    });

    cargarDetalleRegistro();
})();
