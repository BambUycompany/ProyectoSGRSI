const API_PRESTAMOS = "../index.php?ruta=prestamos";

const cuerpoTablaPrestamos = document.getElementById("cuerpoTablaPrestamos");
const formularioPrestamo = document.getElementById("formularioRegistroPrestamo");

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();
    if (!texto.trim()) {
        throw new Error(`La API respondió sin cuerpo (HTTP ${respuesta.status}).`);
    }
    let json;
    try {
        json = JSON.parse(texto);
    } catch {
        throw new Error(`HTTP ${respuesta.status}: La API no devolvió JSON.`);
    }
    if (!respuesta.ok) {
        throw new Error(`HTTP ${respuesta.status}: ${json.mensaje ?? "La solicitud no se pudo completar."}`);
    }
    return json.datos;
}

function cabecerasMutacion() {
    return {
        "Content-Type": "application/json",
        "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
    };
}

async function obtenerPortatilesDisponibles() {
    const respuesta = await fetch(`${API_PRESTAMOS}?recurso=portatiles_disponibles`);
    return await leerRespuestaAPI(respuesta);
}

async function obtenerMisPrestamos() {
    const respuesta = await fetch(`${API_PRESTAMOS}?recurso=prestamo`);
    return await leerRespuestaAPI(respuesta);
}

async function registrarPrestamo(datosPrestamo) {
    const respuesta = await fetch(`${API_PRESTAMOS}?recurso=prestamo`, {
        method: "POST",
        headers: cabecerasMutacion(),
        body: JSON.stringify(datosPrestamo)
    });
    return await leerRespuestaAPI(respuesta);
}

async function finalizarPrestamo(prestamoId) {
    if (!window.confirm("¿Confirmar devolución de este portátil?")) {
        return;
    }
    try {
        const respuesta = await fetch(`${API_PRESTAMOS}?recurso=prestamo`, {
            method: "PATCH",
            headers: cabecerasMutacion(),
            body: JSON.stringify({ prestamoId })
        });
        await leerRespuestaAPI(respuesta);
        await actualizarTablaPrestamos();
    } catch (error) {
        window.alert(error.message);
    }
}

function agregarFilaPrestamo(prestamo) {
    const fila = document.createElement("tr");

    const campos = [prestamo.PortatilModelo, prestamo.CIAlumno, prestamo.Clase, prestamo.FechaPrestamo, prestamo.FechaDev, prestamo.Estado];
    for (const valor of campos) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    const campoOperaciones = document.createElement("td");
    if (prestamo.Estado === "activo") {
        const btnFinalizar = document.createElement("button");
        btnFinalizar.type = "button";
        btnFinalizar.textContent = "Marcar como devuelto";
        btnFinalizar.classList.add("btnOperacion");
        btnFinalizar.addEventListener("click", () => finalizarPrestamo(prestamo.ID));
        campoOperaciones.appendChild(btnFinalizar);
    }
    fila.appendChild(campoOperaciones);

    cuerpoTablaPrestamos.appendChild(fila);
}

async function actualizarTablaPrestamos() {
    cuerpoTablaPrestamos.replaceChildren();
    try {
        const prestamos = await obtenerMisPrestamos();
        for (const prestamo of prestamos) {
            agregarFilaPrestamo(prestamo);
        }
    } catch (error) {
        window.alert("No se pudieron cargar los préstamos: " + error.message);
    }
}

async function cargarSelectPortatiles() {
    const selectPortatil = document.getElementById("portatilId");
    try {
        const disponibles = await obtenerPortatilesDisponibles();
        selectPortatil.innerHTML = '<option value="">Seleccionar</option>';
        for (const portatil of disponibles) {
            const opcion = document.createElement("option");
            opcion.value = portatil.ID ?? portatil.id;
            opcion.textContent = portatil.Modelo ?? portatil.modelo;
            selectPortatil.appendChild(opcion);
        }
    } catch (error) {
        window.alert("No se pudieron cargar los portátiles disponibles: " + error.message);
    }
}

async function gestionarRegistroPrestamo(eventoFormulario) {
    eventoFormulario.preventDefault();
    try {
        const datosFormulario = new FormData(formularioPrestamo);

        const datosPrestamo = {
            portatilId: Number(datosFormulario.get("portatilId")),
            fechaDev: datosFormulario.get("fechaDev"),
            ciAlumno: datosFormulario.get("ciAlumno"),
            clase: datosFormulario.get("clase"),
            correoAlumno: datosFormulario.get("correoAlumno"),
            telefonoAlumno: datosFormulario.get("telefonoAlumno")
        };

        await registrarPrestamo(datosPrestamo);

        formularioPrestamo.reset();
        window.alert("Préstamo registrado correctamente.");
        await actualizarTablaPrestamos();
        await cargarSelectPortatiles();
    } catch (error) {
        window.alert(error.message);
    }
}

if (formularioPrestamo) {
    formularioPrestamo.addEventListener("submit", gestionarRegistroPrestamo);
    cargarSelectPortatiles();
}

actualizarTablaPrestamos();