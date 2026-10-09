
(function () {
    const API_PLANILLA = "../index.php?ruta=planilla";

    const cuerpoTabla = document.getElementById("cuerpoTablaRegistros");
    const mensajeRegistros = document.getElementById("mensajeRegistros");
    const mensajeSinRegistros = document.getElementById("mensajeSinRegistros");
    const botonesPeriodo = document.querySelectorAll("[data-periodo]");

    let periodoActual = "todo";
    let numeroConsulta = 0;

    function mostrarMensaje(mensaje) {
        mensajeRegistros.textContent = mensaje;
        mensajeRegistros.className = "mensajeRegistros error";
        mensajeRegistros.hidden = false;
    }

    function manejarError(error) {
        if (error.status === 401 || error.status === 403) {
            sessionStorage.setItem("errorAcceso", error.message);
            window.location.replace("./login.html");
            return;
        }

        mostrarMensaje(error.message);
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
        return hora ? String(hora).slice(0, 5) : "-";
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

        fila.appendChild(crearCelda(formatearFecha(planilla.Fecha)));

        fila.appendChild(
            crearCelda(
                formatearHora(planilla.HoraEntrada) +
                " - " +
                formatearHora(planilla.HoraSalida)
            )
        );

        fila.appendChild(crearCelda(aula));

        const celdaAcciones = document.createElement("td");
        const enlace = document.createElement("a");

        enlace.href = "./detalle_registros.html?id=" +
            encodeURIComponent(planilla.ID);

        enlace.className = "botones";
        enlace.textContent = "Ver detalle";

        celdaAcciones.appendChild(enlace);
        fila.appendChild(celdaAcciones);

        cuerpoTabla.appendChild(fila);
    }

    async function consultarRegistros(periodo) {
        const parametros = new URLSearchParams({
            ruta: "planilla",
            vista: "propios",
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
                resultado.mensaje ?? "No se pudieron obtener tus registros."
            );

            error.status = respuesta.status;

            throw error;
        }

        if (!Array.isArray(resultado.datos?.planillas)) {
            throw new Error(
                "Los registros recibidos no tienen el formato esperado."
            );
        }

        return resultado.datos.planillas;
    }

    function actualizarBotones() {
        for (const boton of botonesPeriodo) {
            const seleccionado = boton.dataset.periodo === periodoActual;

            boton.classList.toggle("activo", seleccionado);
            boton.setAttribute("aria-pressed", String(seleccionado));
        }
    }

    async function cargarMisRegistros() {
        const consultaActual = ++numeroConsulta;

        cuerpoTabla.replaceChildren();

        mensajeRegistros.hidden = true;
        mensajeSinRegistros.hidden = true;

        try {
            const planillas = await consultarRegistros(periodoActual);

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

    for (const boton of botonesPeriodo) {
        boton.addEventListener("click", function () {
            periodoActual = boton.dataset.periodo;

            actualizarBotones();
            cargarMisRegistros();
        });
    }

    actualizarBotones();
    cargarMisRegistros();
})();
