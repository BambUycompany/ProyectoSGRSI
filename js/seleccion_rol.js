

const ROLES_VALIDOS = ["administrador", "solicitante", "soporte"];
 
const rolActual = sessionStorage.getItem("rolActual");
 
// Muestra la <section> del <main> cuyo id coincide con el rol.
function mostrarSeccionDelRol() {
    const seccion = document.getElementById(rolActual);
    if (seccion !== null) {
        seccion.style.display = "block";
    }
}
 
// Muestra cada <li data-roles> del navbar solo si su lista incluye al rol actual.
function aplicarPermisosNavbar() {
    const itemsNavbar = document.querySelectorAll(".listaNavegacion li[data-roles]");
    for (const item of itemsNavbar) {
        const rolesPermitidos = item.dataset.roles.split(" ");
        item.style.display = rolesPermitidos.includes(rolActual) ? "" : "none";
    }
}
 
// Abre/cierra el menú del icono de usuario usando la clase .visible del CSS.
function configurarMenuUsuario() {
    const btnIconoUsuario = document.getElementById("btnIconoUsuario");
    const opcionesUsuario = document.getElementById("opcionesUsuario");
    if (btnIconoUsuario === null || opcionesUsuario === null) return;
 
    // Click en el icono: alterna la clase .visible.
    btnIconoUsuario.addEventListener("click", (evento) => {
        // Evita que el click llegue al document y cierre el menú al instante.
        evento.stopPropagation();
        opcionesUsuario.classList.toggle("visible");
    });
 
    // Click en cualquier otro lado: cierra el menú.
    document.addEventListener("click", () => {
        opcionesUsuario.classList.remove("visible");
    });
}
 
// Cierra la sesión: limpia sessionStorage y vuelve al login.
function cerrarSesion() {
    sessionStorage.clear();
    window.location.href = "login.html";
}
 
function iniciar() {
    // Retorna a login si no hay rol o no es válido.
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