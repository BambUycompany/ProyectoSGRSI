const btnAgregarAula = document.getElementById("btnAgregarAula");
const btnCerrarAgregarAula = document.getElementById("btnCerrarModal");
const dialogAgregarAula = document.getElementById("dialogAgregarAula");
const formAgregarAula = document.getElementById("formAgregarAula");

const dialogModificarAula = document.getElementById("dialogModificarAula");
const btnCerrarModificarAula = document.getElementById("btnCerrarModalModificar");
const formModificarAula = document.getElementById("formModificarAula");
const inputModificarId = document.getElementById("modificarAulaId");
const selectModificarTipo = document.getElementById("modificarTipo");
const inputModificarNumero = document.getElementById("modificarNumero");

function abrirAgregarAula() {
    formAgregarAula.reset();
    dialogAgregarAula.showModal();
}

function cerrarAgregarAula() {
    dialogAgregarAula.close();
}
document.querySelectorAll(".btnModificar").forEach(boton => {
    boton.addEventListener("click", (e) => {
        const id = e.target.getAttribute("data-id");
        const tipo = e.target.getAttribute("data-tipo").toLowerCase().trim();
        const numero = e.target.getAttribute("data-numero");

        inputModificarId.value = id;
        selectModificarTipo.value = tipo;
        inputModificarNumero.value = numero;

        dialogModificarAula.showModal();
    });
});

// Cerrar modal Modificar
if (btnCerrarModificarAula) {
    btnCerrarModificarAula.addEventListener("click", () => dialogModificarAula.close());
}

dialogAgregarAula?.addEventListener("cancel", () => formAgregarAula.reset());
dialogModificarAula?.addEventListener("cancel", () => formModificarAula.reset());

btnAgregarAula.addEventListener("click", abrirAgregarAula);
btnCerrarAgregarAula.addEventListener("click", cerrarAgregarAula);
dialogAgregarAula.addEventListener("cancel", () => formAgregarAula.reset());