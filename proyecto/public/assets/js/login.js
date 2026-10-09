
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

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();

    let json;

    try {
        json = JSON.parse(texto);
    } catch {
        throw new Error(
            `HTTP ${respuesta.status}: La API no devolvió JSON válido.`
        );
    }

    if (!respuesta.ok) {
        throw new Error(
            `HTTP ${respuesta.status}: ${json.mensaje ?? "Error al iniciar sesión."}`
        );
    }

    return json.datos;
}

async function loguear(cedula, clave) {

    const respuesta = await fetch(API_LOGIN, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ cedula, clave })
    });

    return await leerRespuestaAPI(respuesta);
}

async function gestionarLogin(eventoFormulario) {

    eventoFormulario.preventDefault();
    mensajeError.textContent = "";

    try {

        const sesion = await loguear(
            entradaCedula.value.trim(),
            entradaClave.value
        );
        if (
            !sesion.csrfToken ||
            !sesion.cedula ||
            !Array.isArray(sesion.roles) ||
            sesion.roles.length === 0
        ) {
            throw new Error(
                "La API devolvió datos de sesión incompletos."
            );
        }

        sessionStorage.clear();

        sessionStorage.setItem("csrfToken", sesion.csrfToken);
        sessionStorage.setItem("cedula", sesion.cedula);
        sessionStorage.setItem("nombre", sesion.nombre);
        sessionStorage.setItem("apellido", sesion.apellido);
        sessionStorage.setItem(
            "roles",
            JSON.stringify(sesion.roles)
        );
        if (sesion.roles.length > 1) {

            sessionStorage.removeItem("rolActivo");

            window.location.replace(
                "./seleccion_dashboard.html"
            );

            return;
        }

        const rolActivo = sesion.roles[0];

        const pagina = PAGINAS_POR_ROL[rolActivo];

        if (!pagina) {
            throw new Error(
                "No existe un dashboard para el rol asignado."
            );
        }

        sessionStorage.setItem("rolActivo", rolActivo);

        window.location.replace(pagina);

    } catch (error) {

        mensajeError.textContent = error.message;
    }
}

if (formularioLogin) {
    formularioLogin.addEventListener(
        "submit",
        gestionarLogin
    );
}
