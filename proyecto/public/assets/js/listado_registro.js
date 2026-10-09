
(function () {
    const API_PLANILLA = "../index.php?ruta=planilla";

    const cuerpoTabla = document.getElementById("cuerpoTablaRegistros");
    const mensajeRegistros = document.getElementById("mensajeRegistros");
    const mensajeSinRegistros = document.getElementById("mensajeSinRegistros");
    const filtroPeriodo = document.getElementById("filtroPeriodo");

    function mostrarMensaje(mensaje, tipo = "error") {
        mensajeRegistros.textContent = mensaje;
        mensajeRegistros.className = "mensajeRegistros " + tipo;
        mensajeRegistros.hidden = false;
    }

    function ocultarMensaje() {
        mensajeRegistros.textContent = "";
        mensajeRegistros.hidden = true;
    }

    function manejarError(error) {
        if (error.status === 401 || error.status === 403) {
            sessionStorage.setItem("errorAcceso", error.message);
            window.location.replace("./login.html");
            return;
        }

        mostrarMensaje(error.message);
    }

    async function consultarPlanillas(periodo) {
        const parametros = new URLSearchParams({
            ruta: "planilla",
            periodo: periodo
        });

        const respuesta = await fetch(
            "../index.php?" + parametros.toString(),
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
                resultado.mensaje ?? "No se pudieron cargar las planillas."
            );

            error.status = respuesta.status;
            throw error;
        }

        if (!Array.isArray(resultado.datos?.planillas)) {
            throw new Error(
                "El listado recibido no tiene el formato esperado."
            );
        }

        return resultado.datos.planillas;
    }

    function crearCelda(valor) {
        const celda = document.createElement("td");
        celda.textContent = valor ?? "-";
        return celda;
    }

    function formatearFecha(fecha) {
        if (!fecha) {
            return "-";
        }

        const partes = String(fecha).split("-");

        if (partes.length !== 3) {
            return fecha;
        }

        return partes[2] + "/" + partes[1] + "/" + partes[0];
    }

    function formatearHora(hora) {
        if (!hora) {
            return "-";
        }

        return String(hora).slice(0, 5);
    }

    function agregarFila(planilla) {
        const fila = document.createElement("tr");

        const aulaTipo = String(planilla.AulaTipo ?? "");
        const aulaNumero = String(planilla.AulaNumero ?? "");

        const aula = (
            aulaTipo.charAt(0).toUpperCase() +
            aulaTipo.slice(1) +
            " " +
            aulaNumero
        ).trim();

        fila.appendChild(crearCelda(planilla.ID));
        fila.appendChild(crearCelda(formatearFecha(planilla.Fecha)));
        fila.appendChild(crearCelda(aula));
        fila.appendChild(crearCelda(formatearHora(planilla.HoraEntrada)));
        fila.appendChild(crearCelda(formatearHora(planilla.HoraSalida)));
        fila.appendChild(crearCelda(planilla.NombreSolicitante));

        const celdaAcciones = document.createElement("td");
        const enlaceDetalle = document.createElement("a");

        enlaceDetalle.href = "./detalle_registros.html?id=" +
            encodeURIComponent(planilla.ID);

        enlaceDetalle.className = "botones";
        enlaceDetalle.textContent = "Ver detalle";

        celdaAcciones.appendChild(enlaceDetalle);
        fila.appendChild(celdaAcciones);

        cuerpoTabla.appendChild(fila);
    }

    let numeroConsulta = 0;

    async function cargarRegistros() {
        const consultaActual = ++numeroConsulta;
        const periodo = filtroPeriodo.value;

        ocultarMensaje();
        mensajeSinRegistros.hidden = true;
        cuerpoTabla.replaceChildren();

        try {
            const planillas = await consultarPlanillas(periodo);

            if (consultaActual !== numeroConsulta) {
                return;
            }

            if (planillas.length === 0) {
                mensajeSinRegistros.hidden = false;
                return;
            }

            for (const planilla of planillas) {
                agregarFila(planilla);
            }

        } catch (error) {
            if (consultaActual === numeroConsulta) {
                manejarError(error);
            }
        }
    }

    filtroPeriodo.addEventListener("change", cargarRegistros);

    cargarRegistros();
})();
