const btnAltaEmpleado = document.getElementById("btnAgregarEmpleado");
const btnCerrarAgregarEmpleado = document.getElementById("btnCerrarAgregarEmpleado");
const dialogAgregarEmpleado = document.querySelector(".dialogAgregarEmpleado");

const listadoEmpleados = document.getElementById("listadoTablaEmpleados");
const formAgregarEmpleado = document.getElementById("formAgregarEmpleado");
const colOperaciones = document.getElementById("colOperaciones");

const inputCedula = document.getElementById("cedula");
const inputNombre = document.getElementById("nombre");
const inputApellido = document.getElementById("apellido");
const inputRol = document.getElementById("rol");
const inputEmail = document.getElementById("email");

let empleadoEnEdicion = false;

function limpiarEstadoGestionarEmpleado() {
    empleadoEnEdicion = false;
    inputCedula.readOnly = false;
    formAgregarEmpleado.reset();
}

function abrirAltaEmpleado() {
    limpiarEstadoGestionarEmpleado();
    dialogAgregarEmpleado.showModal();
}

function cerrarAltaEmpleado() {
    limpiarEstadoGestionarEmpleado();
    dialogAgregarEmpleado.close();
}

function abrirModificarEmpleado(cedula) {
    const empleados = cargarEmpleadosGuardadosLocal();
    const empleadoAModificar = empleados.find(emp => emp.cedula === cedula);
    if (empleadoAModificar === undefined) {
        alert("Empleado no encontrado");
        return;
    }

    empleadoEnEdicion = true;
    inputCedula.value = empleadoAModificar.cedula;
    inputNombre.value = empleadoAModificar.nombre;
    inputApellido.value = empleadoAModificar.apellido;
    inputEmail.value = empleadoAModificar.email;
    inputRol.value = empleadoAModificar.rol;
    inputCedula.readOnly = true;
    dialogAgregarEmpleado.showModal();
}

function cargarEmpleadosGuardadosLocal() {
    const empleadosGuardados = localStorage.getItem("empleados");
    if (empleadosGuardados === null) return [];
    return JSON.parse(empleadosGuardados);
}

function actualizarEmpleadosGuardadosLocal(empleados) {
    localStorage.setItem("empleados", JSON.stringify(empleados));
}

function obtenerDatosFormularioEmpleado() {
    const cedula = inputCedula.value.trim();
    const nombre = inputNombre.value.trim();
    const apellido = inputApellido.value.trim();
    const email = inputEmail.value.trim();
    const rol = inputRol.value.trim();

    const empleado = {
        cedula: cedula,
        nombre: nombre,
        apellido: apellido,
        email: email,
        rol: rol
    };

    return empleado;
}

function agregarFilaEmpleado(empleado) {
    const fila = document.createElement("tr");
    const campoCedula = document.createElement("td");
    campoCedula.textContent = empleado.cedula;

    const campoNombre = document.createElement("td");
    campoNombre.textContent = empleado.nombre;

    const campoApellido = document.createElement("td");
    campoApellido.textContent = empleado.apellido;

    const campoEmail = document.createElement("td");
    campoEmail.textContent = empleado.email;

    const campoRol = document.createElement("td");
    campoRol.textContent = empleado.rol;

    const campoOperaciones = document.createElement("td");

    const cajaOperaciones = document.createElement("div");
    cajaOperaciones.classList.add("cajaOperaciones");

    const btnModificar = document.createElement("button");
    btnModificar.type = "button";
    btnModificar.textContent = "Modificar";
    btnModificar.classList.add("btnOperacion");
    btnModificar.addEventListener("click", () => {
        abrirModificarEmpleado(empleado.cedula);
    });

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.classList.add("btnOperacion");
    btnEliminar.addEventListener("click", () => {
        if (confirm("¿Está seguro que desea eliminar este empleado?")) {
            eliminarEmpleadoLocal(empleado.cedula);
        }
    });

    cajaOperaciones.appendChild(btnModificar);
    cajaOperaciones.appendChild(btnEliminar);
    campoOperaciones.appendChild(cajaOperaciones);

    fila.appendChild(campoCedula);
    fila.appendChild(campoNombre);
    fila.appendChild(campoApellido);
    fila.appendChild(campoEmail);
    fila.appendChild(campoRol);
    fila.appendChild(campoOperaciones);

    listadoEmpleados.appendChild(fila);
}

function actualizarTabla() {
    listadoEmpleados.replaceChildren();
    const empleados = cargarEmpleadosGuardadosLocal();

    colOperaciones.style.display = empleados.length > 0 ? "" : "none";

    for (const empleado of empleados) {
        agregarFilaEmpleado(empleado);
    }
}

function guardarEmpleadoLocal(empleadoEnFormulario) {
    const empleados = cargarEmpleadosGuardadosLocal();

    const yaExiste = empleados.some(emp => emp.cedula === empleadoEnFormulario.cedula);
    if (yaExiste) {
        alert("Ya existe un empleado con esa cédula");
        return;
    }

    empleados.push(empleadoEnFormulario);
    actualizarEmpleadosGuardadosLocal(empleados);
}

function modificarEmpleadoLocal(empleadoEnFormulario) {
    const empleados = cargarEmpleadosGuardadosLocal();
    const empleadoAModificar = empleados.find(emp => emp.cedula === empleadoEnFormulario.cedula);

    if (empleadoAModificar === undefined) {
        return;
    }

    empleadoAModificar.nombre = empleadoEnFormulario.nombre;
    empleadoAModificar.apellido = empleadoEnFormulario.apellido;
    empleadoAModificar.email = empleadoEnFormulario.email;
    empleadoAModificar.rol = empleadoEnFormulario.rol;

    actualizarEmpleadosGuardadosLocal(empleados);
}

function eliminarEmpleadoLocal(cedula) {
    const empleados = cargarEmpleadosGuardadosLocal();
    const empleadosRestantes = empleados.filter(emp => emp.cedula !== cedula);
    actualizarEmpleadosGuardadosLocal(empleadosRestantes);
    actualizarTabla();
}

function gestionarEmpleado(evento) {
    evento.preventDefault();
    const empleadoEnFormulario = obtenerDatosFormularioEmpleado();

    if (empleadoEnEdicion) {
        modificarEmpleadoLocal(empleadoEnFormulario);
    } else {
        guardarEmpleadoLocal(empleadoEnFormulario);
    }

    cerrarAltaEmpleado();
    actualizarTabla();
}

formAgregarEmpleado.addEventListener("submit", gestionarEmpleado);
btnAltaEmpleado.addEventListener("click", abrirAltaEmpleado);
btnCerrarAgregarEmpleado.addEventListener("click", cerrarAltaEmpleado);

dialogAgregarEmpleado.addEventListener("cancel", limpiarEstadoGestionarEmpleado);

actualizarTabla();
