
(function () {
    const API_TICKETS = "../index.php?ruta=tickets&accion=listarAgrupados";

    const cuerpoTabla = document.getElementById("listadoTablaTickets");
    const mensajeTickets = document.getElementById("mensajeTickets");
    const mensajeSinTickets = document.getElementById("mensajeSinTickets");

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

    async function consultarTickets() {
        const respuesta = await fetch(API_TICKETS, {
            method: "GET",
            cache: "no-store"
        });

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
                resultado.mensaje ?? "No se pudieron obtener los tickets."
            );

            error.status = respuesta.status;
            throw error;
        }

        if (!Array.isArray(resultado.datos?.tickets)) {
            throw new Error(
                "El listado recibido no tiene el formato esperado."
            );
        }

        return resultado.datos.tickets;
    }

    function crearCelda(valor) {
        const celda = document.createElement("td");
        celda.textContent = valor ?? "";
        return celda;
    }

    function agregarFila(grupo) {
        const fila = document.createElement("tr");

        const numeroPc = String(grupo.PcNumPc ?? "");
        const aulaId = String(grupo.PcAulaID ?? "");
        const aulaTipo = String(grupo.AulaTipo ?? "");
        const aulaNumero = String(grupo.AulaNumero ?? "");
        const cantidad = Number(grupo.CantidadReportes ?? 0);

        fila.appendChild(crearCelda(numeroPc));
        fila.appendChild(
            crearCelda(
                aulaTipo.charAt(0).toUpperCase() +
                aulaTipo.slice(1) +
                " " +
                aulaNumero
            )
        );
        fila.appendChild(crearCelda(cantidad));

        const celdaAcciones = document.createElement("td");

        const enlace = document.createElement("a");
        const parametros = new URLSearchParams({
            pc: numeroPc,
            aulaId: aulaId
        });

        enlace.href = "./detalle_tickets.html?" + parametros.toString();
        enlace.className = "botones";
        enlace.textContent = "Ver detalle";

        celdaAcciones.appendChild(enlace);
        fila.appendChild(celdaAcciones);

        cuerpoTabla.appendChild(fila);
    }

    async function cargarListadoTickets() {
        ocultarMensaje();
        mensajeSinTickets.hidden = true;
        cuerpoTabla.replaceChildren();

        try {
            const ticketsAgrupados = await consultarTickets();

            if (ticketsAgrupados.length === 0) {
                mensajeSinTickets.hidden = false;
                return;
            }

            for (const grupo of ticketsAgrupados) {
                agregarFila(grupo);
            }

        } catch (error) {
            manejarError(error);
        }
    }

    cargarListadoTickets();
})();
