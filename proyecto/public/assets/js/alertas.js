

async function cerrarToastSGRSI(evento) {
    const boton = evento.currentTarget;
    const aviso = boton.closest(".toast-sgrsi");

    if (aviso) {
        aviso.remove();
    }
}

async function eliminarToastSGRSI(aviso) {
    if (aviso && aviso.isConnected) {
        aviso.remove();
    }
}

async function mostrarToast(mensaje, tipo = "info") {
    let contenedor = document.getElementById("contenedorToastSGRSI");

    if (!contenedor) {
        contenedor = document.createElement("div");
        contenedor.id = "contenedorToastSGRSI";
        contenedor.className = "contenedor-toast-sgrsi";
        contenedor.setAttribute("aria-live", "polite");
        document.body.appendChild(contenedor);
    }

    const tiposValidos = ["exito", "error", "advertencia", "info"];
    const tipoFinal = tiposValidos.includes(tipo) ? tipo : "info";

    const aviso = document.createElement("div");
    aviso.className = `toast-sgrsi toast-sgrsi--${tipoFinal}`;
    aviso.setAttribute(
        "role",
        tipoFinal === "error" ? "alert" : "status"
    );

    const texto = document.createElement("span");
    texto.className = "toast-sgrsi__texto";
    texto.textContent = String(mensaje);

    const botonCerrar = document.createElement("button");
    botonCerrar.type = "button";
    botonCerrar.className = "toast-sgrsi__cerrar";
    botonCerrar.textContent = "×";
    botonCerrar.setAttribute("aria-label", "Cerrar notificación");

    botonCerrar.addEventListener("click", cerrarToastSGRSI);

    aviso.appendChild(texto);
    aviso.appendChild(botonCerrar);

    contenedor.appendChild(aviso);

    const duracion = tipoFinal === "error" ? 7500 : 5000;

    window.setTimeout(eliminarToastSGRSI, duracion, aviso);
}
