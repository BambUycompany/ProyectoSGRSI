const btnMenu= document.getElementById("btnMenu");
const btnCerrarMenu = document.getElementById("btnCerrarMenu");
const listaNavegacion = document.querySelector(".listaNavegacion");

function abrirMenu(){
    listaNavegacion.classList.add("visible");
    btnCerrarMenu.classList.add("visible");
    btnMenu.classList.add("oculto");
}

function cerrarMenu(){
listaNavegacion.classList.remove("visible");
    btnCerrarMenu.classList.remove("visible");
    btnMenu.classList.remove("oculto");
}

btnMenu.addEventListener("click", abrirMenu);
btnCerrarMenu.addEventListener("click",cerrarMenu);
const btnIconoUsuario = document.getElementById("btnIconoUsuario");
const opcionesUsuario = document.getElementById("opcionesUsuario");
const btnCerrarSesion = document.getElementById("btnCerrarSesion");
const btnCambiarRol = document.getElementById("btnCambiarRol");

btnIconoUsuario.addEventListener("click", (e) => {
    e.stopPropagation();
    opcionesUsuario.classList.toggle("visible");
});

document.addEventListener("click", (e) => {
    if (!opcionesUsuario.contains(e.target) && e.target !== btnIconoUsuario) {
        opcionesUsuario.classList.remove("visible");
    }
});

btnCerrarSesion.addEventListener("click", () => {
    window.location.href = "Cerrar_sesion.php";
});

if (btnCambiarRol) {
    btnCambiarRol.addEventListener("click", () => {
        window.location.href = "seleccion_dashboard.php";
    });
}