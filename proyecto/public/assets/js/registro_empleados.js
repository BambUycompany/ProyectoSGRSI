const btnAgregarEmpleado = document.getElementById("btnAgregarEmpleado");
const btnCerrarAgregarEmpleado = document.getElementById("btnCerrarAgregarEmpleado");
const dialogAgregarEmpleado = document.querySelector(".dialogAgregarEmpleado");

const cuerpoTablaEmpleados = document.getElementById("cuerpoTablaEmpleados");
const formAgregarEmpleado = document.getElementById("formAgregarEmpleado");

const entradaCedula = document.getElementById("cedula");
const entradaNombre = document.getElementById("nombre");
const entradaApellido = document.getElementById("apellido");
const entradaClave = document.getElementById("claveHash");
const entradaConfirmarClave = document.getElementById("confirmarClave");
const entradaRol = document.getElementById("rol");

let modoFormulario = "";

const formulariosEliminar = document.querySelectorAll(".formularioEliminarEmpleado");

function rolATextoValue(textoRol) {
    const mapa = {
        "Administrador": "administrador",
        "Soporte": "soporte",
        "Solicitante": "solicitante"
    };
    return mapa[textoRol] ?? "";
}

function limpiarEstadoGestionarEmpleado() {
    modoFormulario = "";
    entradaCedula.readOnly = false;
    formAgregarEmpleado.reset();
}

function abrirAltaEmpleado() {
    limpiarEstadoGestionarEmpleado();
    modoFormulario = "alta";
    dialogAgregarEmpleado.showModal();
}

function cerrarGestionarEmpleado() {
    limpiarEstadoGestionarEmpleado();
    dialogAgregarEmpleado.close();
}

function confirmarEliminacion(eventoEliminar) {
    const confirmacion = confirm("¿Está seguro de eliminar usuario?");
    if (!confirmacion) {
        eventoEliminar.preventDefault();
    }
}

function abrirModificarEmpleado(eventoModificar) {
    const btnModificar = eventoModificar.target.closest(".btnModificar");
    if (btnModificar === null) return;

    const fila = btnModificar.closest("tr");

    entradaCedula.readOnly = true;
    formAgregarEmpleado.reset();
    modoFormulario = "modificar";

    entradaCedula.value = fila.cells[0].textContent.trim();
    entradaNombre.value = fila.cells[1].textContent.trim();
    entradaApellido.value = fila.cells[2].textContent.trim();

    // Como el texto de la celda puede ser "Administrador, Soporte" (varios roles), tomamos solo el primero para precargar
    const primerRol = fila.cells[3].textContent.trim().split(",")[0].trim();
    entradaRol.value = rolATextoValue(primerRol);

    dialogAgregarEmpleado.showModal();
}

function gestionarEmpleado(evento) {
    if (modoFormulario === "alta") {
        formAgregarEmpleado.action = "../app/controlador/procesarAltaUsuario.php";
    } else if (modoFormulario === "modificar") {
        formAgregarEmpleado.action = "../app/controlador/procesarModificarUsuario.php";
    } else {
        evento.preventDefault();
    }
}

btnAgregarEmpleado.addEventListener("click", abrirAltaEmpleado);
cuerpoTablaEmpleados.addEventListener("click", abrirModificarEmpleado);
btnCerrarAgregarEmpleado.addEventListener("click", cerrarGestionarEmpleado);
dialogAgregarEmpleado.addEventListener("cancel", limpiarEstadoGestionarEmpleado);
formAgregarEmpleado.addEventListener("submit", gestionarEmpleado);

for (const formulario of formulariosEliminar) {
    formulario.addEventListener("submit", confirmarEliminacion);
}