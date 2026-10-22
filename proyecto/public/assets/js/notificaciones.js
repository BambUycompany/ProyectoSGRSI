// Notificaciones del rol solicitante.
const API_NOTIFICACIONES = "../index.php?ruta=notificaciones";
let notificacionesIniciadas = false;
let cargandoNotificaciones = false;

async function solicitarNotificaciones(accion, metodo = "GET", datos = null) {
    const opciones = { method: metodo, cache: "no-store" };

    if (metodo !== "GET") {
        opciones.headers = {
            "Content-Type": "application/json",
            "X-CSRF-Token": sessionStorage.getItem("csrfToken") || ""
        };
        opciones.body = JSON.stringify(datos);
    }

    const respuesta = await fetch(`${API_NOTIFICACIONES}&accion=${accion}`, opciones);
    const resultado = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(resultado.mensaje || "No se pudieron gestionar las notificaciones.");
    }

    return resultado.datos;
}

function mostrarMensajeNotificaciones(mensaje, error = false) {
    const lista = document.getElementById("listaNotificaciones");
    if (!lista) return;

    const parrafo = document.createElement("p");
    parrafo.className = error ? "notifMensaje notifError" : "notifMensaje";
    parrafo.textContent = mensaje;
    lista.replaceChildren(parrafo);
}

function actualizarContadorNotificaciones(cantidad) {
    const contador = document.getElementById("contadorNotificaciones");
    const campana = document.getElementById("btnCampanaNotif");
    if (!contador || !campana) return;

    const numero = Number(cantidad) || 0;
    contador.hidden = numero === 0;
    contador.textContent = numero > 99 ? "99+" : String(numero);
    campana.setAttribute("aria-label", `Notificaciones: ${numero} sin leer`);
}

function fechaNotificacion(valor) {
    if (!valor) return "";
    const fecha = new Date(String(valor).replace(" ", "T"));
    return Number.isNaN(fecha.getTime()) ? String(valor) : fecha.toLocaleString("es-UY");
}

async function abrirRegistroNotificacion(notificacion) {
    const planillaId = Number(notificacion.PlanillaId);
    if (!Number.isInteger(planillaId) || planillaId <= 0) {
        mostrarMensajeNotificaciones("La notificación no tiene un registro asociado válido.", true);
        return;
    }

    try {
        if (Number(notificacion.Leida) !== 1) {
            await solicitarNotificaciones("marcarLeida", "PUT", { id: Number(notificacion.ID) });
        }
        window.location.href = `./detalle_registros.html?id=${encodeURIComponent(planillaId)}`;
    } catch (error) {
        mostrarMensajeNotificaciones(error.message, true);
    }
}

function crearElementoNotificacion(notificacion) {
    const articulo = document.createElement("article");
    articulo.className = "notifItem";
    if (Number(notificacion.Leida) === 0) articulo.classList.add("notifNoLeida");

    const titulo = document.createElement("h4");
    const tipo = notificacion.AulaTipo || "aula";
    const aula = notificacion.AulaNumero ?? "-";
    const pc = notificacion.PcNumPc ?? "-";
    titulo.textContent = `${pc} del ${tipo} ${aula} resuelta`;

    const diagnostico = document.createElement("p");
    diagnostico.className = "notifDiagnostico";
    diagnostico.textContent = `Diagnóstico: ${notificacion.Diagnostico || "Sin diagnóstico registrado."}`;

    const fecha = document.createElement("small");
    fecha.textContent = fechaNotificacion(notificacion.FechaCreacion);

    const boton = document.createElement("button");
    boton.type = "button";
    boton.className = "notifVerRegistro";
    boton.textContent = "Ver registro completo";
    boton.addEventListener("click", () => abrirRegistroNotificacion(notificacion));

    articulo.append(titulo, diagnostico, fecha, boton);
    return articulo;
}

async function cargarNotificaciones() {
    if (cargandoNotificaciones || sessionStorage.getItem("rolActivo") !== "solicitante") return;
    const lista = document.getElementById("listaNotificaciones");
    if (!lista) return;

    cargandoNotificaciones = true;
    try {
        const datos = await solicitarNotificaciones("listar");
        const notificaciones = Array.isArray(datos.notificaciones) ? datos.notificaciones : [];
        actualizarContadorNotificaciones(datos.sinLeer);

        if (notificaciones.length === 0) {
            mostrarMensajeNotificaciones("Todavía no tenés notificaciones.");
        } else {
            lista.replaceChildren(...notificaciones.map(crearElementoNotificacion));
        }
    } catch (error) {
        mostrarMensajeNotificaciones(error.message, true);
    } finally {
        cargandoNotificaciones = false;
    }
}

function iniciarNotificaciones() {
    if (notificacionesIniciadas || sessionStorage.getItem("rolActivo") !== "solicitante") return;

    const campana = document.getElementById("btnCampanaNotif");
    const dialogo = document.getElementById("dialogNotificaciones");
    if (!campana || !dialogo) return;

    notificacionesIniciadas = true;
    campana.addEventListener("click", () => {
        dialogo.showModal();
        cargarNotificaciones();
    });

    document.getElementById("btnCerrarDialog")?.addEventListener("click", () => dialogo.close());
    dialogo.addEventListener("click", evento => {
        if (evento.target === dialogo) dialogo.close();
    });

    cargarNotificaciones();
    setInterval(() => {
        if (!document.hidden) cargarNotificaciones();
    }, 60000);
}

// Compatible con las páginas que ya llaman SGRSINotificaciones.iniciar().
window.SGRSINotificaciones = {
    iniciar: iniciarNotificaciones,
    cargarNotificaciones
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", iniciarNotificaciones);
} else {
    iniciarNotificaciones();
}
