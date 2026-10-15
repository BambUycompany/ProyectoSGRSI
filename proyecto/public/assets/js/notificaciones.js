
(function () {
    "use strict";

    const API = "../index.php?ruta=notificaciones";

    let iniciado = false;
    let actualizando = false;

    function mostrarError(mensaje) {
        const contenedor = document.getElementById("listaNotificaciones");

        if (!contenedor) {
            return;
        }

        const parrafo = document.createElement("p");
        parrafo.className = "notifMensaje notifError";
        parrafo.textContent = mensaje;

        contenedor.replaceChildren(parrafo);
    }

    async function peticion(accion, metodo = "GET", datos = null) {
        const url = new URL(API, window.location.href);

        url.searchParams.set("accion", accion);

        const opciones = {
            method: metodo,
            cache: "no-store"
        };

        if (metodo !== "GET") {
            opciones.headers = {
                "Content-Type": "application/json",
                "X-CSRF-Token":
                    sessionStorage.getItem("csrfToken") ?? ""
            };

            opciones.body = JSON.stringify(datos);
        }

        const respuesta = await fetch(url, opciones);

        let resultado;

        try {
            resultado = await respuesta.json();
        } catch {
            throw new Error(
                "El servidor no devolvió una respuesta JSON válida."
            );
        }

        if (!respuesta.ok) {
            throw new Error(
                resultado.mensaje ??
                "No se pudieron consultar las notificaciones."
            );
        }

        return resultado.datos;
    }

    function actualizarContador(cantidad) {
        const contador = document.getElementById(
            "contadorNotificaciones"
        );

        const campana = document.getElementById(
            "btnCampanaNotif"
        );

        if (!contador || !campana) {
            return;
        }

        const numero = Number(cantidad) || 0;

        contador.hidden = numero === 0;

        contador.textContent = numero > 99
            ? "99+"
            : String(numero);

        campana.setAttribute(
            "aria-label",
            numero === 1
                ? "Notificaciones: 1 sin leer"
                : `Notificaciones: ${numero} sin leer`
        );
    }

    function fechaLegible(valor) {
        if (!valor) {
            return "";
        }

        const fecha = new Date(
            String(valor).replace(" ", "T")
        );

        return Number.isNaN(fecha.getTime())
            ? String(valor)
            : fecha.toLocaleString("es-UY");
    }

    async function abrirRegistro(notificacion) {
        const idNotificacion = Number(notificacion.ID);
        const planillaId = Number(notificacion.PlanillaId);

        if (!Number.isInteger(planillaId) || planillaId <= 0) {
            mostrarError(
                "La notificación no tiene un registro asociado válido."
            );
            return;
        }

        if (Number(notificacion.Leida) !== 1) {
            try {
                await peticion(
                    "marcarLeida",
                    "PUT",
                    { id: idNotificacion }
                );
            } catch (error) {
                mostrarError(error.message);
                return;
            }
        }

        window.location.href =
            `./detalle_registros.html?id=${encodeURIComponent(planillaId)}`;
    }

    function crearNotificacion(notificacion) {
        const articulo = document.createElement("article");
        articulo.className = "notifItem";

        if (Number(notificacion.Leida) === 0) {
            articulo.classList.add("notifNoLeida");
        }

        const titulo = document.createElement("h4");

        const tipo = String(
            notificacion.AulaTipo ?? "aula"
        );

        const aula = String(
            notificacion.AulaNumero ?? "-"
        );

        const pc = String(
            notificacion.PcNumPc ?? "-"
        );

        titulo.textContent =
            `${pc} del ${tipo} ${aula} resuelta`;

        const diagnostico = document.createElement("p");
        diagnostico.className = "notifDiagnostico";

        diagnostico.textContent =
            "Diagnóstico: " +
            (
                notificacion.Diagnostico ||
                "Sin diagnóstico registrado."
            );

        const fecha = document.createElement("small");

        fecha.textContent = fechaLegible(
            notificacion.FechaCreacion
        );

        const enlace = document.createElement("button");
        enlace.type = "button";
        enlace.className = "notifVerRegistro";
        enlace.textContent = "Ver registro completo";

        enlace.addEventListener("click", function () {
            abrirRegistro(notificacion);
        });

        articulo.append(
            titulo,
            diagnostico,
            fecha,
            enlace
        );

        return articulo;
    }

    async function cargarNotificaciones() {
        if (
            actualizando ||
            sessionStorage.getItem("rolActivo") !== "solicitante"
        ) {
            return;
        }

        actualizando = true;

        try {
            const datos = await peticion("listar");

            const lista = document.getElementById(
                "listaNotificaciones"
            );

            if (!lista) {
                return;
            }

            const notificaciones = Array.isArray(
                datos.notificaciones
            )
                ? datos.notificaciones
                : [];

            actualizarContador(datos.sinLeer);

            if (notificaciones.length === 0) {
                const mensaje = document.createElement("p");
                mensaje.className = "notifMensaje";

                mensaje.textContent =
                    "Todavía no tenés notificaciones.";

                lista.replaceChildren(mensaje);
                return;
            }

            lista.replaceChildren(
                ...notificaciones.map(crearNotificacion)
            );

        } catch (error) {
            mostrarError(error.message);

        } finally {
            actualizando = false;
        }
    }

    function iniciar() {
        if (
            iniciado ||
            sessionStorage.getItem("rolActivo") !== "solicitante"
        ) {
            return;
        }

        const campana = document.getElementById(
            "btnCampanaNotif"
        );

        const dialogo = document.getElementById(
            "dialogNotificaciones"
        );

        if (!campana || !dialogo) {
            return;
        }

        iniciado = true;

        campana.addEventListener("click", function () {
            dialogo.showModal();
            cargarNotificaciones();
        });

        document.getElementById("btnCerrarDialog")
            ?.addEventListener("click", function () {
                dialogo.close();
            });

        dialogo.addEventListener("click", function (evento) {
            if (evento.target === dialogo) {
                dialogo.close();
            }
        });

        cargarNotificaciones();

        window.setInterval(function () {
            if (!document.hidden) {
                cargarNotificaciones();
            }
        }, 60000);
    }

    window.SGRSINotificaciones = {
        iniciar,
        cargarNotificaciones
    };

    if (document.readyState === "loading") {
        document.addEventListener(
            "DOMContentLoaded",
            iniciar,
            { once: true }
        );
    } else {
        iniciar();
    }
})();
