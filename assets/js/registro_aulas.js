const btnAltaAula = document.getElementById("btnAgregarAula");
const btnCerrarAgregarAula = document.getElementById("btnCerrarAgregarAula");
const dialogAgregarAula = document.querySelector(".dialogAgregarAula");

const listadoAulas = document.getElementById("listadoTablaAulas");
const formAgregarAula = document.getElementById("formAgregarAula");
const colOperaciones = document.getElementById("colOperaciones");

const inputTipo = document.getElementById("tipo");
const inputNumero = document.getElementById("numero");
const inputCantidad = document.getElementById("cantidad");

let aulaEnEdicion = false;
let idAulaEnEdicion = null;

function limpiarEstadoGestionarAula() {
    aulaEnEdicion = false;
    idAulaEnEdicion = null;
    formAgregarAula.reset();
}

function abrirAltaAula() {
    limpiarEstadoGestionarAula();
    dialogAgregarAula.showModal();
}

function cerrarAltaAula() {
    limpiarEstadoGestionarAula();
    dialogAgregarAula.close();
}

function abrirModificarAula(id) {
    const aulas = cargarAulasGuardadasLocal();
    const aulaAModificar = aulas.find(aula => aula.id === id);
    if (aulaAModificar === undefined) {
        alert("Aula no encontrada");
        return;
    }

    aulaEnEdicion = true;
    idAulaEnEdicion = id;
    inputTipo.value = aulaAModificar.tipo;
    inputNumero.value = aulaAModificar.numero;
    inputCantidad.value = aulaAModificar.cantidad;
    dialogAgregarAula.showModal();
}

function cargarAulasGuardadasLocal() {
    const aulasGuardadas = localStorage.getItem("aulas");
    if (aulasGuardadas === null) return [];
    return JSON.parse(aulasGuardadas);
}

function actualizarAulasGuardadasLocal(aulas) {
    localStorage.setItem("aulas", JSON.stringify(aulas));
}

function obtenerDatosFormularioAula() {
    const tipo = inputTipo.value.trim();
    const numero = inputNumero.value.trim();
    const cantidad = inputCantidad.value.trim();

    const aula = {
        tipo: tipo,
        numero: numero,
        cantidad: cantidad
    };

    return aula;
}

function agregarFilaAula(aula) {
    const fila = document.createElement("tr");
    const campoId = document.createElement("td");
    campoId.textContent = aula.id;

    const campoTipo = document.createElement("td");
    campoTipo.textContent = aula.tipo;

    const campoNumero = document.createElement("td");
    campoNumero.textContent = aula.numero;

    const campoCantidad = document.createElement("td");
    campoCantidad.textContent = aula.cantidad;

    const campoOperaciones = document.createElement("td");

    const cajaOperaciones = document.createElement("div");
    cajaOperaciones.classList.add("cajaOperaciones");

    const btnModificar = document.createElement("button");
    btnModificar.type = "button";
    btnModificar.textContent = "Modificar";
    btnModificar.classList.add("btnOperacion");
    btnModificar.addEventListener("click", () => {
        abrirModificarAula(aula.id);
    });

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.classList.add("btnOperacion");
    btnEliminar.addEventListener("click", () => {
        if (confirm("¿Está seguro que desea eliminar esta aula?")) {
            eliminarAulaLocal(aula.id);
        }
    });

    cajaOperaciones.appendChild(btnModificar);
    cajaOperaciones.appendChild(btnEliminar);
    campoOperaciones.appendChild(cajaOperaciones);

    fila.appendChild(campoId);
    fila.appendChild(campoTipo);
    fila.appendChild(campoNumero);
    fila.appendChild(campoCantidad);
    fila.appendChild(campoOperaciones);

    listadoAulas.appendChild(fila);
}

function actualizarTabla() {
    listadoAulas.replaceChildren();
    const aulas = cargarAulasGuardadasLocal();

    colOperaciones.style.display = aulas.length > 0 ? "" : "none";

    for (const aula of aulas) {
        agregarFilaAula(aula);
    }
}

// El aula no tiene una clave única ingresada por el usuario (como la cédula
// del empleado), así que generamos un ID incremental a partir del mayor existente.
function generarNuevoId(aulas) {
    if (aulas.length === 0) return 1;
    const idsExistentes = aulas.map(aula => aula.id);
    return Math.max(...idsExistentes) + 1;
}

function guardarAulaLocal(aulaEnFormulario) {
    const aulas = cargarAulasGuardadasLocal();

    const yaExiste = aulas.some(aula => aula.tipo === aulaEnFormulario.tipo && aula.numero === aulaEnFormulario.numero);
    if (yaExiste) {
        alert("Ya existe un aula con ese tipo y número");
        return;
    }

    aulaEnFormulario.id = generarNuevoId(aulas);
    aulas.push(aulaEnFormulario);
    actualizarAulasGuardadasLocal(aulas);
}

function modificarAulaLocal(aulaEnFormulario) {
    const aulas = cargarAulasGuardadasLocal();
    const aulaAModificar = aulas.find(aula => aula.id === idAulaEnEdicion);

    if (aulaAModificar === undefined) {
        return;
    }

    aulaAModificar.tipo = aulaEnFormulario.tipo;
    aulaAModificar.numero = aulaEnFormulario.numero;
    aulaAModificar.cantidad = aulaEnFormulario.cantidad;

    actualizarAulasGuardadasLocal(aulas);
}

function eliminarAulaLocal(id) {
    const aulas = cargarAulasGuardadasLocal();
    const aulasRestantes = aulas.filter(aula => aula.id !== id);
    actualizarAulasGuardadasLocal(aulasRestantes);
    actualizarTabla();
}

function gestionarAula(evento) {
    evento.preventDefault();
    const aulaEnFormulario = obtenerDatosFormularioAula();

    if (aulaEnEdicion) {
        modificarAulaLocal(aulaEnFormulario);
    } else {
        guardarAulaLocal(aulaEnFormulario);
    }

    cerrarAltaAula();
    actualizarTabla();
}

formAgregarAula.addEventListener("submit", gestionarAula);
btnAltaAula.addEventListener("click", abrirAltaAula);
btnCerrarAgregarAula.addEventListener("click", cerrarAltaAula);

dialogAgregarAula.addEventListener("cancel", limpiarEstadoGestionarAula);

actualizarTabla();