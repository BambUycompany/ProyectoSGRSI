const btnAgregarEquipo = document.getElementById("btnAgregarEquipo");
const btnCerrarAgregarEquipo = document.getElementById("btnCerrarAgregarEquipo");
const dialogAgregarEquipo = document.getElementById("dialogAgregarEquipo");
const formAgregarEquipo = document.getElementById("formAgregarEquipo");

const btnCerrarModificarEquipo = document.getElementById("btnCerrarModificarEquipo");
const dialogModificarEquipo = document.getElementById("dialogModificarEquipo");

const inputModificarNumPc = document.getElementById("modificarNumPc");
const inputModificarModeloPc = document.getElementById("modificarModeloPc");
const inputModificarMonitor = document.getElementById("modificarMonitor");
const inputModificarModeloMouse = document.getElementById("modificarModeloMouse");
const inputModificarModeloTeclado = document.getElementById("modificarModeloTeclado");

const botonesModificar = document.querySelectorAll(".btnModificarEquipo");

function abrirAgregarEquipo() {
    formAgregarEquipo.reset();
    dialogAgregarEquipo.showModal();
}

function cerrarAgregarEquipo() {
    dialogAgregarEquipo.close();
}

function abrirModificarEquipo(evento) {
    const boton = evento.currentTarget;

    inputModificarNumPc.value = boton.dataset.numpc;
    inputModificarModeloPc.value = boton.dataset.modelopc;
    inputModificarMonitor.value = boton.dataset.monitor;
    inputModificarModeloMouse.value = boton.dataset.mouse;
    inputModificarModeloTeclado.value = boton.dataset.teclado;

    dialogModificarEquipo.showModal();
}

function cerrarModificarEquipo() {
    dialogModificarEquipo.close();
}

btnAgregarEquipo.addEventListener("click", abrirAgregarEquipo);
btnCerrarAgregarEquipo.addEventListener("click", cerrarAgregarEquipo);
dialogAgregarEquipo.addEventListener("cancel", () => formAgregarEquipo.reset());

btnCerrarModificarEquipo.addEventListener("click", cerrarModificarEquipo);

for (const boton of botonesModificar) {
    boton.addEventListener("click", abrirModificarEquipo);
}