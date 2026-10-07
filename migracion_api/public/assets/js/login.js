const API_LOGIN = "../index.php?ruta=login";

const formularioLogin = document.getElementById("formularioLogin");
const entradaCedula = document.getElementById("cedula");
const entradaClave = document.getElementById("clave");
const mensajeError = document.getElementById("mensajeError");

const PAGINAS_POR_ROL = {
    administrador: "./administrador.html",
    soporte: "./soporte.html",
    solicitante: "./solicitante.html"
};

async function loguear(cedula, clave) {
    const credenciales = { cedula, clave };

    const respuesta = await fetch(API_LOGIN, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(credenciales)
    });

    const json = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(json.mensaje ?? "No se pudo iniciar sesión.");
    }

    return json.datos;
}

async function gestionarLogin(eventoFormulario) {
    eventoFormulario.preventDefault();
    mensajeError.textContent = "";

    try {
        const sesion = await loguear(entradaCedula.value.trim(), entradaClave.value);

        if (!sesion.csrfToken) {
            throw new Error("La API no devolvió el token CSRF.");
        }

        // Las páginas HTML ya no tienen acceso a $_SESSION del servidor —
        // todo lo que necesiten leer (nombre, roles, etc.) se guarda acá.
        sessionStorage.setItem("csrfToken", sesion.csrfToken);
        sessionStorage.setItem("cedula", sesion.cedula);
        sessionStorage.setItem("nombre", sesion.nombre);
        sessionStorage.setItem("apellido", sesion.apellido);
        sessionStorage.setItem("roles", JSON.stringify(sesion.roles));

        // Si tiene un solo rol, va directo a su panel.
        // Si tiene varios, arranca por el primero (el selector de rol lo cambia después).
        const rolDestino = sesion.rolActivo ?? sesion.roles[0];
        sessionStorage.setItem("rolActivo", rolDestino);

        const pagina = PAGINAS_POR_ROL[rolDestino] ?? "./login.html";
        window.location.replace(pagina);
    } catch (error) {
        mensajeError.textContent = error.message;
    }
}

formularioLogin.addEventListener("submit", gestionarLogin);