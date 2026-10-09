
const API_PRESTAMOS = "../index.php?ruta=prestamos";
 
const formularioPrestamo = document.getElementById("formularioRegistroPrestamo");
const selectPortatil = document.getElementById("portatilId");
const mensajeSinPortatiles = document.getElementById("mensajeSinPortatiles");
const cuerpoTablaPrestamos = document.getElementById("cuerpoTablaPrestamos");
const mensajeSinPrestamos = document.getElementById("mensajeSinPrestamos");


const rolActivoPrestamos = sessionStorage.getItem("rolActivo");


async function leerRespuestaAPI(respuesta) {

    const texto = await respuesta.text();

    if (!texto.trim()) {
        throw new Error(
            `La API respondió sin contenido (HTTP ${respuesta.status}).`
        );
    }

    let json;

    try {
        json = JSON.parse(texto);
    } catch {
        console.error("Respuesta de la API:", texto);

        throw new Error(
            `HTTP ${respuesta.status}: La API no devolvió JSON válido.`
        );
    }

    if (!respuesta.ok) {
        throw new Error(
            `HTTP ${respuesta.status}: ${json.mensaje ?? "Error en la solicitud."}`
        );
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

    const respuesta = await fetch(
        `${API_PRESTAMOS}&recurso=portatiles_disponibles`
    );

    return await leerRespuestaAPI(respuesta);
}


async function obtenerPrestamos() {

    const vista = rolActivoPrestamos === "soporte"
        ? "todos"
        : "propios";

    const respuesta = await fetch(
        `${API_PRESTAMOS}&recurso=prestamo&vista=${vista}`
    );

    return await leerRespuestaAPI(respuesta);
}


async function registrarPrestamo(datos) {

    const respuesta = await fetch(
        `${API_PRESTAMOS}&recurso=prestamo`,
        {
            method: "POST",
            headers: cabecerasMutacion(),
            body: JSON.stringify(datos)
        }
    );

    return await leerRespuestaAPI(respuesta);
}


async function finalizarPrestamo(prestamoId) {

    if (rolActivoPrestamos !== "soporte") {
        window.alert("Solo soporte puede confirmar devoluciones.");
        return;
    }

    if (!window.confirm("¿Confirmar que el portátil fue devuelto físicamente?")) {
        return;
    }

    try {
        const respuesta = await fetch(
            `${API_PRESTAMOS}&recurso=prestamo`,
            {
                method: "PATCH",
                headers: cabecerasMutacion(),
                body: JSON.stringify({
                    prestamoId: prestamoId
                })
            }
        );

        await leerRespuestaAPI(respuesta);

        window.alert("Devolución registrada correctamente.");

        await actualizarTablaPrestamos();

    } catch (error) {
        window.alert(error.message);
    }
}


function agregarFilaPrestamo(prestamo) {

    if (!cuerpoTablaPrestamos) {
        return;
    }

    const fila = document.createElement("tr");

    
    const campos = [
        prestamo.PortatilModelo,
        prestamo.CIAlumno,
        prestamo.Clase,
        prestamo.FechaPrestamo,
        prestamo.Estado
    ];


    for (const valor of campos) {

        const celda = document.createElement("td");
        celda.textContent = valor ?? "-";

        fila.appendChild(celda);
    }

    const celdaAcciones = document.createElement("td");

    if (
        prestamo.Estado === "activo" &&
        rolActivoPrestamos === "soporte"
    ) {

        const btnFinalizar = document.createElement("button");

        btnFinalizar.type = "button";
        btnFinalizar.textContent = "Marcar como devuelto";
        btnFinalizar.classList.add("btnOperacion");

        btnFinalizar.addEventListener("click", () => {
            finalizarPrestamo(prestamo.ID);
        });

        celdaAcciones.appendChild(btnFinalizar);

    } else {
        celdaAcciones.textContent = "—";
    }

    fila.appendChild(celdaAcciones);
    cuerpoTablaPrestamos.appendChild(fila);
}


async function actualizarTablaPrestamos() {

    if (!cuerpoTablaPrestamos) {
        return;
    }

    try {
        const prestamos = await obtenerPrestamos();

        cuerpoTablaPrestamos.replaceChildren();

        for (const prestamo of prestamos) {
            agregarFilaPrestamo(prestamo);
        }

        if (mensajeSinPrestamos) {
            mensajeSinPrestamos.hidden = prestamos.length > 0;
        }

    } catch (error) {
        window.alert(
            "No se pudieron cargar los préstamos: " + error.message
        );
    }
}


async function cargarSelectPortatiles() {

    if (!selectPortatil) {
        return;
    }

    try {
        const portatiles = await obtenerPortatilesDisponibles();

        selectPortatil.replaceChildren();

        const opcionInicial = document.createElement("option");

        opcionInicial.value = "";
        opcionInicial.textContent = "Seleccionar";

        selectPortatil.appendChild(opcionInicial);

        for (const portatil of portatiles) {

            const opcion = document.createElement("option");

            opcion.value = portatil.ID;
            opcion.textContent = portatil.Modelo;

            selectPortatil.appendChild(opcion);
        }

        selectPortatil.disabled = portatiles.length === 0;

        if (mensajeSinPortatiles) {
            mensajeSinPortatiles.hidden = portatiles.length > 0;
        }

    } catch (error) {
        window.alert(
            "No se pudieron cargar los portátiles: " + error.message
        );
    }
}


async function gestionarRegistroPrestamo(evento) {

    evento.preventDefault();

    if (rolActivoPrestamos !== "solicitante") {
        window.alert("Solo el docente puede registrar préstamos.");
        return;
    }

    if (!formularioPrestamo.reportValidity()) {
        return;
    }

    try {
        const formulario = new FormData(formularioPrestamo);

        const datos = {
            portatilId: Number(formulario.get("portatilId")),
            ciAlumno: formulario.get("ciAlumno"),
            clase: formulario.get("clase"),
            correoAlumno: formulario.get("correoAlumno"),
            telefonoAlumno: formulario.get("telefonoAlumno")
        };

        await registrarPrestamo(datos);

        window.alert("Préstamo registrado correctamente.");

        formularioPrestamo.reset();

        await cargarSelectPortatiles();

    } catch (error) {
        window.alert(error.message);
    }
}


if (formularioPrestamo) {

    formularioPrestamo.addEventListener(
        "submit",
        gestionarRegistroPrestamo
    );

    if (rolActivoPrestamos === "solicitante") {
        cargarSelectPortatiles();
    }
}

if (cuerpoTablaPrestamos) {
    actualizarTablaPrestamos();
}
