
const PAGINAS_POR_ROL = {
    administrador: "./administrador.html",
    soporte: "./soporte.html",
    solicitante: "./solicitante.html"
};

const contenedorOpciones = document.getElementById("opcionesRol");

let roles = [];

try {
    roles = JSON.parse(
        sessionStorage.getItem("roles") ?? "[]"
    );
} catch {
    roles = [];
}

function elegirRol(rol) {

    if (!roles.includes(rol)) {
        window.alert("No tenés permiso para seleccionar ese rol.");
        return;
    }

    const pagina = PAGINAS_POR_ROL[rol];

    if (!pagina) {
        window.alert("El dashboard solicitado no existe.");
        return;
    }

    sessionStorage.setItem("rolActivo", rol);

    window.location.replace(pagina);
}

function mostrarOpciones() {

    if (
        !sessionStorage.getItem("cedula") ||
        !sessionStorage.getItem("csrfToken") ||
        !Array.isArray(roles) ||
        roles.length === 0
    ) {
        window.location.replace("./login.html");
        return;
    }

    if (roles.length === 1) {
        elegirRol(roles[0]);
        return;
    }

if (!contenedorOpciones) {
    console.error("No se encontró el elemento opcionesRol.");
    return;
}

contenedorOpciones.replaceChildren();
    for (const rol of roles) {

        if (!PAGINAS_POR_ROL[rol]) {
            continue;
        }

        const boton = document.createElement("button");

        boton.type = "button";

        boton.classList.add("btn-rol");

        boton.textContent =
            rol.charAt(0).toUpperCase() + rol.slice(1);

        boton.addEventListener("click", () => {
            elegirRol(rol);
        });

        contenedorOpciones.appendChild(boton);
    }
}

mostrarOpciones();
