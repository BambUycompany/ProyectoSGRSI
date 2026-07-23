

const ROLES_VALIDOS = ["administrador", "solicitante", "soporte"];
 
const rolActual = sessionStorage.getItem("rolActual");
 
function mostrarSeccionDelRol() {
    const seccion = document.getElementById(rolActual);
    if (seccion !== null) {
        seccion.style.display = "block";
    }
}
 
function aplicarPermisosNavbar() {
    const itemsNavbar = document.querySelectorAll(".listaNavegacion li[data-roles]");
    for (const item of itemsNavbar) {
        const rolesPermitidos = item.dataset.roles.split(" ");
        item.style.display = rolesPermitidos.includes(rolActual) ? "" : "none";
    }
}
 
function configurarMenuUsuario() {
    const btnIconoUsuario = document.getElementById("btnIconoUsuario");
    const opcionesUsuario = document.getElementById("opcionesUsuario");
    if (btnIconoUsuario === null || opcionesUsuario === null) return;
 
    btnIconoUsuario.addEventListener("click", (evento) => {
        evento.stopPropagation();
        opcionesUsuario.classList.toggle("visible");
    });
 
    document.addEventListener("click", () => {
        opcionesUsuario.classList.remove("visible");
    });
}
 
function cerrarSesion() {
    sessionStorage.clear();
    window.location.href = "login.html";
}
 
function iniciar() {
    if (rolActual === null || !ROLES_VALIDOS.includes(rolActual)) {
        window.location.href = "login.html";
        return;
    }
 
    mostrarSeccionDelRol();
    aplicarPermisosNavbar();
    configurarMenuUsuario();
 
    const btnCerrarSesion = document.getElementById("btnCerrarSesion");
    if (btnCerrarSesion !== null) {
        btnCerrarSesion.addEventListener("click", cerrarSesion);
    }
}
 
iniciar();