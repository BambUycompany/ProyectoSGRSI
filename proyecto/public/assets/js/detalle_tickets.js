
(function () {
    const API_TICKETS = "../index.php?ruta=tickets";

    const parametros = new URLSearchParams(window.location.search);
    const numeroPc = parametros.get("pc");
    const aulaId = parametros.get("aulaId");

    const tituloEquipo = document.getElementById("tituloEquipo");
    const cuerpoTabla = document.getElementById("cuerpoTablaTickets");
    const mensajeTickets = document.getElementById("mensajeTickets");
    const mensajeSinTickets = document.getElementById("mensajeSinTickets");

    const dialogPrioridad = document.getElementById("dlgPrioridad");
    const dialogEstado = document.getElementById("dlgEstado");
    const dialogFinalizar = document.getElementById("dlgFinalizar");
    const dialogDiagnostico = document.getElementById("dlgDiagnostico");

    const inputPrioridadTicketId = document.getElementById("prioridad_ticket_id");
    const inputEstadoTicketId = document.getElementById("estado_ticket_id");
    const inputFinalizarTicketId = document.getElementById("finalizar_ticket_id");

    const selectPrioridad = document.getElementById("selectPrioridad");
    const selectEstado = document.getElementById("selectEstado");
    const txtDiagnostico = document.getElementById("txtDiagnostico");
    const textoDiagnostico = document.getElementById("textoDiagnostico");

    const formPrioridad = document.getElementById("formPrioridad");
    const formEstado = document.getElementById("formEstado");
    const formFinalizar = document.getElementById("formFinalizar");

    function mostrarMensaje(mensaje, tipo = "error") {
        mensajeTickets.textContent = mensaje;
        mensajeTickets.className = "mensajeTickets " + tipo;
        mensajeTickets.hidden = false;
    }

    function ocultarMensaje() {
        mensajeTickets.textContent = "";
        mensajeTickets.hidden = true;
    }

    function manejarError(error) {
        if (error.status === 401 || error.status === 403) {
            sessionStorage.setItem("errorAcceso", error.message);
            window.location.replace("./login.html");
            return;
        }

        mostrarMensaje(error.message);
    }

    async function solicitarAPI(accion, metodo = "GET", datos = null) {
        const url = new URL(API_TICKETS, window.location.href);

        url.searchParams.set("accion", accion);

        if (accion === "listarPorPCAULA") {
            url.searchParams.set("pc", numeroPc);
            url.searchParams.set("aulaId", aulaId);
        }

        const opciones = {
            method: metodo,
            cache: "no-store"
        };

        if (metodo !== "GET") {
            opciones.headers = {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            };

            opciones.body = JSON.stringify(datos);
        }

        const respuesta = await fetch(url, opciones);

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
                resultado.mensaje ?? "No se pudo completar la operación."
            );

            error.status = respuesta.status;
            throw error;
        }

        return resultado.datos;
    }

    function crearCelda(valor) {
        const celda = document.createElement("td");
        celda.textContent = valor ?? "-";
        return celda;
    }

    function crearBoton(texto, clase, accion) {
        const boton = document.createElement("button");

        boton.type = "button";
        boton.textContent = texto;
        boton.className = "btnOperacion " + clase;
        boton.addEventListener("click", accion);

        return boton;
    }

    function formatearFecha(fecha) {
        if (!fecha) {
            return "-";
        }

        const partes = String(fecha).split(" ");
        const fechaPartes = partes[0].split("-");

        if (fechaPartes.length !== 3) {
            return String(fecha);
        }

        const fechaFormateada = [
            fechaPartes[2],
            fechaPartes[1],
            fechaPartes[0]
        ].join("/");

        return fechaFormateada + (partes[1] ? " " + partes[1] : "");
    }

    function abrirPrioridad(ticket) {
        inputPrioridadTicketId.value = ticket.ID;
        selectPrioridad.value = ticket.Prioridad || "media";
        dialogPrioridad.showModal();
    }

    function abrirEstado(ticket) {
        inputEstadoTicketId.value = ticket.ID;
        selectEstado.value = ticket.Estado || "pendiente";
        dialogEstado.showModal();
    }

    function abrirFinalizar(ticket) {
        inputFinalizarTicketId.value = ticket.ID;
        txtDiagnostico.value = "";
        dialogFinalizar.showModal();
    }

    function abrirDiagnostico(ticket) {
        textoDiagnostico.textContent =
            ticket.Incidente || "No se registró un diagnóstico.";

        dialogDiagnostico.showModal();
    }

    function agregarFilaTicket(ticket) {
        const fila = document.createElement("tr");

        fila.appendChild(crearCelda(ticket.ID));
        fila.appendChild(crearCelda(ticket.Fallo));
        fila.appendChild(crearCelda(ticket.Descripcion));
        fila.appendChild(crearCelda(ticket.Estado));
        fila.appendChild(crearCelda(ticket.Prioridad));
        fila.appendChild(crearCelda(formatearFecha(ticket.FechaCreacion)));

        
        const celdaAcciones = document.createElement("td");
        const cajaOperaciones = document.createElement("div");

        cajaOperaciones.className = "cajaOperaciones";

        if (String(ticket.Estado).toLowerCase() === "finalizado") {
            cajaOperaciones.appendChild(
                crearBoton("Ver diagnóstico", "btnDiagnostico", () => {
                    abrirDiagnostico(ticket);
                })
            );
        } else {
            cajaOperaciones.appendChild(
                crearBoton("Prioridad", "btnPrioridad", () => {
                    abrirPrioridad(ticket);
                })
            );

            cajaOperaciones.appendChild(
                crearBoton("Estado", "btnEstado", () => {
                    abrirEstado(ticket);
                })
            );

            cajaOperaciones.appendChild(
                crearBoton("Finalizar ticket", "btnFinalizar", () => {
                    abrirFinalizar(ticket);
                })
            );
        }

        if (sessionStorage.getItem("rolActivo") === "soporte") {
         celdaAcciones.appendChild(enlace);
        }
        fila.appendChild(celdaAcciones);

        cuerpoTabla.appendChild(fila);
    }

    async function cargarTickets() {
        mensajeSinTickets.hidden = true;
        cuerpoTabla.replaceChildren();

        try {
            const datos = await solicitarAPI("listarPorPCAULA");

            if (!Array.isArray(datos.tickets)) {
                throw new Error(
                    "La respuesta del servidor no contiene un listado de tickets válido."
                );
            }

            const tickets = datos.tickets;

            if (tickets.length === 0) {
                tituloEquipo.textContent = "Tickets de " + numeroPc;
                mensajeSinTickets.hidden = false;
                return;
            }

            const primero = tickets[0];

            const aula = (
                String(primero.AulaTipo ?? "") + " " +
                String(primero.AulaNumero ?? "")
            ).trim();

            tituloEquipo.textContent =
                "Tickets de " + numeroPc + " - " + aula;

            for (const ticket of tickets) {
                agregarFilaTicket(ticket);
            }

        } catch (error) {
            manejarError(error);
        }
    }

    async function guardarModificacion(evento, accion, datos, dialog) {
        evento.preventDefault();

        const botonGuardar = evento.submitter;

        if (botonGuardar) {
            botonGuardar.disabled = true;
        }

        try {
            const resultado = await solicitarAPI(
                accion,
                "PUT",
                datos
            );

            dialog.close();

            await cargarTickets();

            mostrarMensaje(
                resultado.mensaje ?? "Operación realizada correctamente.",
                "exito"
            );

        } catch (error) {
            dialog.close();
            manejarError(error);

        } finally {
            if (botonGuardar) {
                botonGuardar.disabled = false;
            }
        }
    }

    formPrioridad.addEventListener("submit", function (evento) {
        guardarModificacion(
            evento,
            "cambiarPrioridad",
            {
                id: Number(inputPrioridadTicketId.value),
                nuevaPrioridad: selectPrioridad.value
            },
            dialogPrioridad
        );
    });

    formEstado.addEventListener("submit", function (evento) {
        guardarModificacion(
            evento,
            "cambiarEstado",
            {
                id: Number(inputEstadoTicketId.value),
                nuevoEstado: selectEstado.value
            },
            dialogEstado
        );
    });

    formFinalizar.addEventListener("submit", function (evento) {
        guardarModificacion(
            evento,
            "finalizar",
            {
                id: Number(inputFinalizarTicketId.value),
                diagnostico: txtDiagnostico.value.trim()
            },
            dialogFinalizar
        );
    });

    document.querySelectorAll(".btnCancelarModal").forEach(boton => {
        boton.addEventListener("click", function () {
            const dialog = boton.closest("dialog");

            if (dialog) {
                dialog.close();
            }
        });
    });

    if (
        !numeroPc ||
        !/^[1-9]\d*$/.test(aulaId ?? "")
    ) {
        mostrarMensaje(
            "No se indicó una PC y un aula válidas."
        );
    } else {
        tituloEquipo.textContent = "Tickets de " + numeroPc;
        ocultarMensaje();
        cargarTickets();
    }
})();
