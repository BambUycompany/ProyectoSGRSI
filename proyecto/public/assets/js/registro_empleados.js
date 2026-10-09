
const API_USUARIOS = "../index.php?ruta=usuarios";

const cuerpoTablaEmpleados = document.getElementById("cuerpoTablaEmpleados");
const formAgregarEmpleado = document.getElementById("formAgregarEmpleado");

const dialogAgregarEmpleado = document.querySelector(".dialogAgregarEmpleado");
const btnAgregarEmpleado = document.getElementById("btnAgregarEmpleado");
const btnCerrarAgregarEmpleado = document.getElementById("btnCerrarAgregarEmpleado");

const mensajeEmpleados = document.getElementById("mensajeEmpleados");

const entradaCedula = document.getElementById("cedula");
const entradaNombre = document.getElementById("nombre");
const entradaApellido = document.getElementById("apellido");
const entradaClave = document.getElementById("claveHash");
const entradaConfirmarClave = document.getElementById("confirmarClave");

const opcionesRoles = document.querySelectorAll('input[name="roles"]');

const tituloDialogEmpleado = document.getElementById("tituloDialogEmpleado");
const btnGuardarEmpleado = document.getElementById("btnGuardarEmpleado");

let modoFormulario = "alta";
let empleadosActuales = [];


function mostrarMensajeEmpleados(mensaje, tipo = "error") {
    if (!mensajeEmpleados) {
        console.error(mensaje);
        return;
    }

    mensajeEmpleados.textContent = mensaje;
    mensajeEmpleados.className = "mensajeEmpleados " + tipo;
    mensajeEmpleados.hidden = false;
     mensajeEmpleados.scrollIntoView({
        behavior: "smooth",
        block: "center"
    });
}


function limpiarMensajeEmpleados() {
    if (!mensajeEmpleados) {
        return;
    }

    mensajeEmpleados.textContent = "";
    mensajeEmpleados.hidden = true;
}


async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();

    let json;

    try {
        json = JSON.parse(texto);
    } catch {
        throw new Error(
            "El servidor no devolvió una respuesta válida."
        );
    }

    if (!respuesta.ok) {
        const error = new Error(
            json.mensaje ?? "No se pudo completar la operación."
        );

        error.status = respuesta.status;
        throw error;
    }

    return json.datos;
}


function cabecerasMutacion() {
    return {
        "Content-Type": "application/json",
        "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
    };
}



function manejarError(error) {

    if (error.status === 401 || error.status === 403) {
        sessionStorage.setItem("errorAcceso", error.message);
        window.location.replace("./login.html");
        return;
    }

    if (dialogAgregarEmpleado && dialogAgregarEmpleado.open) {
        dialogAgregarEmpleado.close();
    }

    mostrarMensajeEmpleados(error.message, "error");
}



async function solicitarUsuarios(metodo = "GET", datos = null) {
    const opciones = {
        method: metodo
    };

    if (metodo !== "GET") {
        opciones.headers = cabecerasMutacion();
        opciones.body = JSON.stringify(datos);
    }

    const respuesta = await fetch(API_USUARIOS, opciones);

    return await leerRespuestaAPI(respuesta);
}


function obtenerRolesEmpleado(empleado) {
    const roles = [];

    if (Number(empleado.administrador) === 1) {
        roles.push("administrador");
    }

    if (Number(empleado.soporte) === 1) {
        roles.push("soporte");
    }

    if (Number(empleado.solicitante) === 1) {
        roles.push("solicitante");
    }

    return roles;
}


function obtenerRolesSeleccionados() {
    return [...opcionesRoles]
        .filter(opcion => opcion.checked)
        .map(opcion => opcion.value);
}


function asignarRolesSeleccionados(roles) {
    for (const opcion of opcionesRoles) {
        opcion.checked = roles.includes(opcion.value);
    }
}


function crearCelda(texto) {
    const celda = document.createElement("td");

    celda.textContent = texto ?? "";

    return celda;
}


function crearBoton(texto, clase, accion) {
    const boton = document.createElement("button");

    boton.type = "button";
    boton.textContent = texto;
    boton.className = "btnOperacion " + clase;

    boton.addEventListener("click", accion);

    return boton;
}


function agregarFilaEmpleado(empleado) {
    const fila = document.createElement("tr");

    const roles = obtenerRolesEmpleado(empleado);

    const nombresRoles = roles.map(rol => {
        return rol.charAt(0).toUpperCase() + rol.slice(1);
    });

    const activo = Number(empleado.activo) === 1;

    fila.appendChild(crearCelda(empleado.cedula));
    fila.appendChild(crearCelda(empleado.nombre));
    fila.appendChild(crearCelda(empleado.apellido));
    fila.appendChild(crearCelda(nombresRoles.join(", ") || "Sin rol"));
    fila.appendChild(crearCelda(activo ? "Activo" : "Inactivo"));

    const celdaAcciones = document.createElement("td");
    const cajaOperaciones = document.createElement("div");

    cajaOperaciones.className = "cajaOperaciones";

    cajaOperaciones.appendChild(
        crearBoton("Modificar", "btnModificar", () => {
            abrirModificarEmpleado(empleado);
        })
    );

    if (activo) {
        cajaOperaciones.appendChild(
            crearBoton("Desactivar", "btnDesactivar", () => {
                cambiarEstadoEmpleado(empleado, false);
            })
        );
    } else {
        cajaOperaciones.appendChild(
            crearBoton("Reactivar", "btnReactivar", () => {
                cambiarEstadoEmpleado(empleado, true);
            })
        );
    }

    celdaAcciones.appendChild(cajaOperaciones);
    fila.appendChild(celdaAcciones);

    cuerpoTablaEmpleados.appendChild(fila);
}


async function actualizarTablaEmpleados() {
    try {
        empleadosActuales = await solicitarUsuarios();

        cuerpoTablaEmpleados.replaceChildren();

        for (const empleado of empleadosActuales) {
            agregarFilaEmpleado(empleado);
        }

        const mensajeSinEmpleados = document.getElementById(
            "mensajeSinEmpleados"
        );

        if (mensajeSinEmpleados) {
            mensajeSinEmpleados.hidden = empleadosActuales.length > 0;
        }

    } catch (error) {
        manejarError(error);
    }
}


function limpiarFormularioEmpleado() {
    formAgregarEmpleado.reset();

    entradaCedula.readOnly = false;
    entradaClave.required = true;
    entradaConfirmarClave.required = true;

    modoFormulario = "alta";

    asignarRolesSeleccionados([]);
}


function abrirAltaEmpleado() {
    limpiarMensajeEmpleados();
    limpiarFormularioEmpleado();

    if (tituloDialogEmpleado) {
        tituloDialogEmpleado.textContent = "Agregar empleado";
    }

    if (btnGuardarEmpleado) {
        btnGuardarEmpleado.textContent = "Registrar empleado";
    }

    dialogAgregarEmpleado.showModal();
}


function abrirModificarEmpleado(empleado) {
    limpiarMensajeEmpleados();
    limpiarFormularioEmpleado();

    modoFormulario = "modificar";

    entradaCedula.value = empleado.cedula;
    entradaNombre.value = empleado.nombre;
    entradaApellido.value = empleado.apellido;

    entradaCedula.readOnly = true;

    entradaClave.required = false;
    entradaConfirmarClave.required = false;

    asignarRolesSeleccionados(
        obtenerRolesEmpleado(empleado)
    );

    if (tituloDialogEmpleado) {
        tituloDialogEmpleado.textContent = "Modificar empleado";
    }

    if (btnGuardarEmpleado) {
        btnGuardarEmpleado.textContent = "Guardar cambios";
    }

    dialogAgregarEmpleado.showModal();
}


function cerrarGestionarEmpleado() {
    if (dialogAgregarEmpleado.open) {
        dialogAgregarEmpleado.close();
    }

    limpiarFormularioEmpleado();
}


function obtenerDatosFormulario() {
    return {
        cedula: entradaCedula.value.trim(),
        nombre: entradaNombre.value.trim(),
        apellido: entradaApellido.value.trim(),
        clave: entradaClave.value,
        confirmarClave: entradaConfirmarClave.value,
        roles: obtenerRolesSeleccionados()
    };
}


async function gestionarEmpleado(evento) {
    evento.preventDefault();

    const datos = obtenerDatosFormulario();

    if (datos.roles.length === 0) {
        mostrarMensajeEmpleados(
            "Debés seleccionar al menos un rol.",
            "error"
        );
        return;
    }

    if (modoFormulario === "alta" && datos.clave.length < 8) {
        mostrarMensajeEmpleados(
            "La contraseña debe contener al menos 8 caracteres.",
            "error"
        );
        return;
    }

    if (
        modoFormulario === "modificar" &&
        datos.clave !== "" &&
        datos.clave.length < 8
    ) {
        mostrarMensajeEmpleados(
            "La nueva contraseña debe contener al menos 8 caracteres.",
            "error"
        );
        return;
    }

    if (datos.clave !== datos.confirmarClave) {
        mostrarMensajeEmpleados(
            "Las contraseñas no coinciden.",
            "error"
        );
        return;
    }

    const metodo = modoFormulario === "alta" ? "POST" : "PUT";

    try {
        if (btnGuardarEmpleado) {
            btnGuardarEmpleado.disabled = true;
        }

        await solicitarUsuarios(metodo, datos);

        cerrarGestionarEmpleado();

        await actualizarTablaEmpleados();

        mostrarMensajeEmpleados(
            metodo === "POST"
                ? "Empleado registrado correctamente."
                : "Empleado modificado correctamente.",
            "exito"
        );

    } catch (error) {
        manejarError(error);

    } finally {
        if (btnGuardarEmpleado) {
            btnGuardarEmpleado.disabled = false;
        }
    }
}


async function cambiarEstadoEmpleado(empleado, reactivar) {
    const accion = reactivar ? "reactivar" : "desactivar";

    const confirmado = window.confirm(
        `¿Confirmás que querés ${accion} a ${empleado.nombre} ${empleado.apellido}?`
    );

    if (!confirmado) {
        return;
    }

    limpiarMensajeEmpleados();

    try {
        if (reactivar) {
            await solicitarUsuarios("PATCH", {
                cedula: empleado.cedula
            });
        } else {
            await solicitarUsuarios("DELETE", {
                cedula: empleado.cedula
            });
        }

        await actualizarTablaEmpleados();

        mostrarMensajeEmpleados(
            reactivar
                ? "Empleado reactivado correctamente."
                : "Empleado desactivado correctamente.",
            "exito"
        );

    } catch (error) {
        manejarError(error);
    }
}


if (btnAgregarEmpleado) {
    btnAgregarEmpleado.addEventListener(
        "click",
        abrirAltaEmpleado
    );
}

if (btnCerrarAgregarEmpleado) {
    btnCerrarAgregarEmpleado.addEventListener(
        "click",
        cerrarGestionarEmpleado
    );
}

if (dialogAgregarEmpleado) {
    dialogAgregarEmpleado.addEventListener(
        "close",
        limpiarFormularioEmpleado
    );
}

if (formAgregarEmpleado) {
    formAgregarEmpleado.addEventListener(
        "submit",
        gestionarEmpleado
    );
}

if (cuerpoTablaEmpleados) {
    actualizarTablaEmpleados();
}
