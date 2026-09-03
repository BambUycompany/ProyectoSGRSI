const btnAgregarAula = document.getElementById("btnAgregarAula");
const btnCerrarAgregarAula = document.getElementById("btnCerrarAgregarAula");
const dialogAgregarAula = document.getElementById("dialogAgregarAula");
const formAgregarAula = document.getElementById("formAgregarAula");

function abrirAgregarAula() {
    formAgregarAula.reset();
    dialogAgregarAula.showModal();
}

function cerrarAgregarAula() {
    dialogAgregarAula.close();
}

btnAgregarAula.addEventListener("click", abrirAgregarAula);
btnCerrarAgregarAula.addEventListener("click", cerrarAgregarAula);
dialogAgregarAula.addEventListener("cancel", () => formAgregarAula.reset());