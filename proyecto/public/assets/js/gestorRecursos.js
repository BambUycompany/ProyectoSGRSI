const API_RECURSOS = "../index.php?recurso=recursos";

const btnAgregarAula = document.getElementById("btnAgregarAula");
const btnCerrarAgregarAula = document.getElementById("btnCerrarAgregarAula");
const dialogAgregarAula = document.querySelector(".dialogAgregarAula");
const cuerpoTablaAulas = document.getElementById("cuerpoTablaAulas");
const formularioAula = document.getElementById("formularioGestionarAula");
const entradaTipoAula = document.getElementById("tipo");
const entradaNumeroAula = document.getElementById("numero");
let aulaEnEdicion = false;
let aulaIdEnEdicion = null;

const dialogEquiposAula = document.getElementById("dialogEquiposAula");
const btnCerrarEquiposAula = document.getElementById("btnCerrarEquiposAula");
const tituloEquiposAula = document.getElementById("tituloEquiposAula");
const grupoNumeroPc = document.getElementById("grupoNumeroPc");
const grupoCantidadEquipos = document.getElementById("grupoCantidadEquipos");
const entradaCantidadEquipos = document.getElementById("cantidadEquipos");
const tituloFormularioEquipo = document.querySelector("#formularioGestionarEquipo h2");
const cuerpoTablaEquipos = document.getElementById("cuerpoTablaEquipos");
const btnAgregarEquipo = document.getElementById("btnAgregarEquipo");
const btnCerrarAgregarEquipo = document.getElementById("btnCerrarAgregarEquipo");
const dialogAgregarEquipo = document.querySelector(".dialogAgregarEquipo");
const formularioEquipo = document.getElementById("formularioGestionarEquipo");
const entradaNumPc = document.getElementById("numPc");
const entradaModeloPc = document.getElementById("modeloPc");
const entradaMonitor = document.getElementById("monitor");
const entradaModeloMouse = document.getElementById("modeloMouse");
const entradaModeloTeclado = document.getElementById("modeloTeclado");
let aulaIdActual = null;
let equipoEnEdicion = false;

const btnAgregarPortatil = document.getElementById("btnAgregarPortatil");
const btnCerrarAgregarPortatil = document.getElementById("btnCerrarAgregarPortatil");
const dialogAgregarPortatil = document.querySelector(".dialogAgregarPortatil");
const cuerpoTablaPortatiles = document.getElementById("cuerpoTablaPortatiles");
const formularioPortatil = document.getElementById("formularioGestionarPortatil");
const entradaModeloPortatil = document.getElementById("modeloPortatil");
let portatilEnEdicion = false;
let portatilIdEnEdicion = null;

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();

    console.log("Respuesta sin procesar de la API:", texto);

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

async function obtenerAulas() {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=aula`);
    return await leerRespuestaAPI(respuesta);
}

async function obtenerAulaConEquipos(aulaId) {
    const respuesta = await fetch(
        `${API_RECURSOS}&identificador=aula&aulaId=${encodeURIComponent(aulaId)}`
    );

    return await leerRespuestaAPI(respuesta);
}

async function altaAula(tipo, numero) {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=aula`, {
        method: "POST",
        headers: cabecerasMutacion(),
        body: JSON.stringify({ recurso: "aula", tipo, numero })
    });

    return await leerRespuestaAPI(respuesta);
}

async function modificarAula(aulaId, tipo, numero) {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=aula`, {
        method: "PUT",
        headers: cabecerasMutacion(),
        body: JSON.stringify({ aulaId, tipo, numero })
    });

    return await leerRespuestaAPI(respuesta);
}

async function eliminarAula(aulaId) {
    if (!window.confirm("¿Está seguro de eliminar esta aula?")) {
        return;
    }

    try {
        const respuesta = await fetch(
            `${API_RECURSOS}&identificador=aula&aulaId=${encodeURIComponent(aulaId)}`,
            {
                method: "DELETE",
                headers: cabecerasMutacion()
            }
        );

        await leerRespuestaAPI(respuesta);
        await actualizarTablaAulas();
    } catch (error) {
        window.alert(error.message);
    }
}

function agregarFilaAula(aula) {
    const fila = document.createElement("tr");

    const campoTipo = document.createElement("td");
    const linkAula = document.createElement("a");

    linkAula.href = "#";
    linkAula.textContent = aula.Tipo ?? aula.tipo;

    linkAula.addEventListener("click", (evento) => {
        evento.preventDefault();
        abrirDetalleAula(aula.ID ?? aula.id);
    });

    campoTipo.appendChild(linkAula);

    const campoNumero = document.createElement("td");
    campoNumero.textContent = aula.Numero ?? aula.numero;

    const campoOperaciones = document.createElement("td");
    const caja = document.createElement("div");
    caja.classList.add("cajaOperaciones");

    const btnModificar = document.createElement("button");
    btnModificar.type = "button";
    btnModificar.textContent = "Modificar";
    btnModificar.classList.add("btnOperacion");
    btnModificar.addEventListener("click", () => abrirModificarAula(aula));

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.classList.add("btnOperacion");
    btnEliminar.addEventListener("click", () => eliminarAula(aula.ID ?? aula.id));

    caja.appendChild(btnModificar);
    caja.appendChild(btnEliminar);
    campoOperaciones.appendChild(caja);

    fila.appendChild(campoTipo);
    fila.appendChild(campoNumero);
    fila.appendChild(campoOperaciones);

    cuerpoTablaAulas.appendChild(fila);
}

async function actualizarTablaAulas() {
    cuerpoTablaAulas.replaceChildren();

    try {
        const resultado = await obtenerAulas();
        const aulas = resultado.aulas ?? resultado;

        for (const aula of aulas) {
            agregarFilaAula(aula);
        }
    } catch (error) {
        window.alert("No se pudieron cargar las aulas: " + error.message);
    }
}

function abrirAltaAula() {
    aulaEnEdicion = false;
    aulaIdEnEdicion = null;
    formularioAula.reset();
    dialogAgregarAula.showModal();
}

function abrirModificarAula(aula) {
    aulaEnEdicion = true;
    aulaIdEnEdicion = aula.ID ?? aula.id;

    entradaTipoAula.value = (aula.Tipo ?? aula.tipo ?? "").toLowerCase();
    entradaNumeroAula.value = aula.Numero ?? aula.numero;

    dialogAgregarAula.showModal();
}

function cerrarAula() {
    formularioAula.reset();
    dialogAgregarAula.close();
}

async function gestionarAula(eventoFormulario) {
    eventoFormulario.preventDefault();

    try {
        const tipo = entradaTipoAula.value;
        const numero = entradaNumeroAula.value.trim();

        if (!aulaEnEdicion) {
            await altaAula(tipo, numero);
        } else {
            await modificarAula(aulaIdEnEdicion, tipo, numero);
        }

        cerrarAula();
        await actualizarTablaAulas();
    } catch (error) {
        window.alert(error.message);
    }
}



async function abrirDetalleAula(aulaId) {
    aulaIdActual = aulaId;

    try {
        const resultado = await obtenerAulaConEquipos(aulaId);

        if (tituloEquiposAula) {
            const aula = resultado.aula ?? {};
            const tipo = aula.Tipo ?? aula.tipo ?? "Aula";
            const numero = aula.Numero ?? aula.numero ?? "";

            tituloEquiposAula.textContent =
                `Equipos — ${tipo} ${numero}`.trim();
        }

        await actualizarTablaEquipos(resultado.equipos ?? []);

        if (!dialogEquiposAula.open) {
            dialogEquiposAula.showModal();
        }
    } catch (error) {
        window.alert(
            "No se pudo abrir el detalle del aula: " + error.message
        );
    }
}

async function agregarEquipo(datosEquipo) {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=equipo`, {
        method: "POST",
        headers: cabecerasMutacion(),
        body: JSON.stringify({
            recurso: "equipo",
            ...datosEquipo
        })
    });

    return await leerRespuestaAPI(respuesta);
}

async function modificarEquipo(datosEquipo) {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=equipo`, {
        method: "PUT",
        headers: cabecerasMutacion(),
        body: JSON.stringify(datosEquipo)
    });

    return await leerRespuestaAPI(respuesta);
}

async function eliminarEquipo(numPc, aulaId) {
    if (!window.confirm(`¿Eliminar el equipo ${numPc}?`)) {
        return;
    }

    try {
        const respuesta = await fetch(
            `${API_RECURSOS}&identificador=equipo&numPc=${encodeURIComponent(numPc)}&aulaId=${encodeURIComponent(aulaId)}`,
            {
                method: "DELETE",
                headers: cabecerasMutacion()
            }
        );

        await leerRespuestaAPI(respuesta);
        await abrirDetalleAula(aulaId);
    } catch (error) {
        window.alert(error.message);
    }
}

function agregarFilaEquipo(equipo) {
    const fila = document.createElement("tr");

    const numPc = equipo.NumPc ?? equipo.numPc ?? equipo.num_pc;
    const modeloPc = equipo.Modelo ?? equipo.modeloPc ?? equipo.modelo_pc;
    const monitor = equipo.Monitor ?? equipo.monitor;
    const mouse = equipo.Mouse ?? equipo.modeloMouse ?? equipo.modelo_mouse;
    const teclado = equipo.Teclado ?? equipo.modeloTeclado ?? equipo.modelo_teclado;

    const campos = [
        numPc,
        modeloPc,
        monitor,
        mouse,
        teclado
    ];

    for (const valor of campos) {
        const celda = document.createElement("td");
        celda.textContent = valor ?? "-";
        fila.appendChild(celda);
    }

    const campoOperaciones = document.createElement("td");
    const caja = document.createElement("div");
    caja.classList.add("cajaOperaciones");

    const btnModificar = document.createElement("button");
    btnModificar.type = "button";
    btnModificar.textContent = "Modificar";
    btnModificar.classList.add("btnOperacion");

    btnModificar.addEventListener("click", () => {
        abrirModificarEquipo(equipo);
    });

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.classList.add("btnOperacion");

    btnEliminar.addEventListener("click", () => {
        eliminarEquipo(numPc, aulaIdActual);
    });

    caja.appendChild(btnModificar);
    caja.appendChild(btnEliminar);

    campoOperaciones.appendChild(caja);
    fila.appendChild(campoOperaciones);

    cuerpoTablaEquipos.appendChild(fila);
}

async function actualizarTablaEquipos(equipos) {
    cuerpoTablaEquipos.replaceChildren();

    for (const equipo of equipos) {
        agregarFilaEquipo(equipo);
    }
}

function abrirAltaEquipo() {
    if (!aulaIdActual) {
        window.alert("Seleccione un aula antes de agregar equipos.");
        return;
    }

    equipoEnEdicion = false;
    formularioEquipo.reset();

    entradaNumPc.value = "";
    grupoNumeroPc.hidden = true;

    grupoCantidadEquipos.hidden = false;
    entradaCantidadEquipos.required = true;
    entradaCantidadEquipos.value = "1";

    tituloFormularioEquipo.textContent = "Agregar equipos";
    dialogAgregarEquipo.showModal();
}

function abrirModificarEquipo(equipo) {
    equipoEnEdicion = true;
    formularioEquipo.reset();

    entradaNumPc.value =
        equipo.NumPc ?? equipo.numPc ?? equipo.num_pc ?? "";

    entradaNumPc.readOnly = true;
    grupoNumeroPc.hidden = false;

    grupoCantidadEquipos.hidden = true;
    entradaCantidadEquipos.required = false;

    entradaModeloPc.value =
        equipo.Modelo ?? equipo.modeloPc ?? equipo.modelo_pc ?? "";

    entradaMonitor.value =
        equipo.Monitor ?? equipo.monitor ?? "";

    entradaModeloMouse.value =
        equipo.Mouse ?? equipo.modeloMouse ?? equipo.modelo_mouse ?? "";

    entradaModeloTeclado.value =
        equipo.Teclado ?? equipo.modeloTeclado ?? equipo.modelo_teclado ?? "";

    tituloFormularioEquipo.textContent = "Modificar equipo";
    dialogAgregarEquipo.showModal();
}

function cerrarEquipo() {
    formularioEquipo.reset();
    dialogAgregarEquipo.close();
}

async function gestionarEquipo(eventoFormulario) {
    eventoFormulario.preventDefault();

    try {
        const datosEquipo = {
            aulaId: aulaIdActual,
            modeloPc: entradaModeloPc.value.trim(),
            monitor: entradaMonitor.value.trim(),
            modeloMouse: entradaModeloMouse.value.trim(),
            modeloTeclado: entradaModeloTeclado.value.trim()
        };

        if (
            !datosEquipo.aulaId ||
            !datosEquipo.modeloPc ||
            !datosEquipo.monitor ||
            !datosEquipo.modeloMouse ||
            !datosEquipo.modeloTeclado
        ) {
            window.alert("Complete todos los campos del equipo.");
            return;
        }

        if (!equipoEnEdicion) {
            const cantidad = Number(entradaCantidadEquipos.value);

            if (
                !Number.isInteger(cantidad) ||
                cantidad < 1 ||
                cantidad > 100
            ) {
                window.alert(
                    "Indique una cantidad entera entre 1 y 100."
                );
                return;
            }

            datosEquipo.cantidad = cantidad;
            await agregarEquipo(datosEquipo);
        } else {
            datosEquipo.numPc = entradaNumPc.value.trim();

            if (!datosEquipo.numPc) {
                window.alert("No se pudo identificar la PC.");
                return;
            }

            await modificarEquipo(datosEquipo);
        }

        cerrarEquipo();
        await abrirDetalleAula(aulaIdActual);
    } catch (error) {
        window.alert(error.message);
    }
}



async function obtenerPortatiles() {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=portatil`);
    return await leerRespuestaAPI(respuesta);
}

async function altaPortatil(modeloPortatil) {
    const respuesta = await fetch(
        `${API_RECURSOS}&identificador=portatil`,
        {
            method: "POST",
            headers: cabecerasMutacion(),
            body: JSON.stringify({ modeloPortatil })
        }
    );

    return await leerRespuestaAPI(respuesta);
}

async function modificarPortatil(portatilId, modeloPortatil) {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=portatil`, {
        method: "PUT",
        headers: cabecerasMutacion(),
        body: JSON.stringify({
            portatilId,
            modeloPortatil
        })
    });

    return await leerRespuestaAPI(respuesta);
}

async function eliminarPortatil(portatilId) {
    if (!window.confirm("¿Eliminar este portátil?")) {
        return;
    }

    try {
        const respuesta = await fetch(
            `${API_RECURSOS}&identificador=portatil&portatilId=${encodeURIComponent(portatilId)}`,
            {
                method: "DELETE",
                headers: cabecerasMutacion()
            }
        );

        await leerRespuestaAPI(respuesta);
        await actualizarTablaPortatiles();
    } catch (error) {
        window.alert(error.message);
    }
}

async function cambiarEstadoPortatil(portatilId, accion) {
    try {
        const respuesta = await fetch(`${API_RECURSOS}&identificador=portatil`, {
            method: "PATCH",
            headers: cabecerasMutacion(),
            body: JSON.stringify({
                portatilId,
                accion
            })
        });

        await leerRespuestaAPI(respuesta);
        await actualizarTablaPortatiles();
    } catch (error) {
        window.alert(error.message);
    }
}

function agregarFilaPortatil(portatil) {
    const fila = document.createElement("tr");

    const campoModelo = document.createElement("td");
    campoModelo.textContent = portatil.modelo ?? portatil.Modelo;

    const campoEstado = document.createElement("td");
    campoEstado.textContent = portatil.estado ?? portatil.Estado;

    const campoOperaciones = document.createElement("td");
    const caja = document.createElement("div");
    caja.classList.add("cajaOperaciones");

    const estado = portatil.estado ?? portatil.Estado;
    const id = portatil.id ?? portatil.ID;

    const btnModificar = document.createElement("button");
    btnModificar.type = "button";
    btnModificar.textContent = "Modificar";
    btnModificar.classList.add("btnOperacion");

    btnModificar.addEventListener("click", () => {
        abrirModificarPortatil(portatil);
    });

    caja.appendChild(btnModificar);

    if (estado === "disponible") {
        const btnEliminar = document.createElement("button");
        btnEliminar.type = "button";
        btnEliminar.textContent = "Eliminar";
        btnEliminar.classList.add("btnOperacion");

        btnEliminar.addEventListener("click", () => {
            eliminarPortatil(id);
        });

        caja.appendChild(btnEliminar);

        const btnDeshabilitar = document.createElement("button");
        btnDeshabilitar.type = "button";
        btnDeshabilitar.textContent = "Deshabilitar";
        btnDeshabilitar.classList.add("btnOperacion");

        btnDeshabilitar.addEventListener("click", () => {
            cambiarEstadoPortatil(id, "deshabilitar");
        });

        caja.appendChild(btnDeshabilitar);
    } else if (estado === "deshabilitado") {
        const btnHabilitar = document.createElement("button");
        btnHabilitar.type = "button";
        btnHabilitar.textContent = "Habilitar";
        btnHabilitar.classList.add("btnOperacion");

        btnHabilitar.addEventListener("click", () => {
            cambiarEstadoPortatil(id, "habilitar");
        });

        caja.appendChild(btnHabilitar);
    }

    campoOperaciones.appendChild(caja);
    fila.appendChild(campoModelo);
    fila.appendChild(campoEstado);
    fila.appendChild(campoOperaciones);

    cuerpoTablaPortatiles.appendChild(fila);
}

async function actualizarTablaPortatiles() {
    cuerpoTablaPortatiles.replaceChildren();

    try {
        const resultado = await obtenerPortatiles();
        const portatiles = resultado.portatiles ?? resultado;

        for (const portatil of portatiles) {
            agregarFilaPortatil(portatil);
        }
    } catch (error) {
        window.alert(
            "No se pudieron cargar los portátiles: " + error.message
        );
    }
}

function abrirAltaPortatil() {
    portatilEnEdicion = false;
    portatilIdEnEdicion = null;

    formularioPortatil.reset();
    dialogAgregarPortatil.showModal();
}

function abrirModificarPortatil(portatil) {
    portatilEnEdicion = true;
    portatilIdEnEdicion = portatil.id ?? portatil.ID;

    entradaModeloPortatil.value =
        portatil.modelo ?? portatil.Modelo;

    dialogAgregarPortatil.showModal();
}

function cerrarPortatil() {
    formularioPortatil.reset();
    dialogAgregarPortatil.close();
}

async function gestionarPortatil(eventoFormulario) {
    eventoFormulario.preventDefault();

    try {
        const modeloPortatil = entradaModeloPortatil.value.trim();

        if (!portatilEnEdicion) {
            await altaPortatil(modeloPortatil);
        } else {
            await modificarPortatil(
                portatilIdEnEdicion,
                modeloPortatil
            );
        }

        cerrarPortatil();
        await actualizarTablaPortatiles();
    } catch (error) {
        window.alert(error.message);
    }
}



if (btnAgregarAula) {
    btnAgregarAula.addEventListener("click", abrirAltaAula);
}

if (btnCerrarAgregarAula) {
    btnCerrarAgregarAula.addEventListener("click", cerrarAula);
}

if (dialogAgregarAula) {
    dialogAgregarAula.addEventListener("cancel", (evento) => {
        evento.preventDefault();
        cerrarAula();
    });
}

if (formularioAula) {
    formularioAula.addEventListener("submit", gestionarAula);
}

if (btnCerrarEquiposAula) {
    btnCerrarEquiposAula.addEventListener("click", () => {
        dialogEquiposAula.close();
    });
}

if (dialogEquiposAula) {
    dialogEquiposAula.addEventListener("close", () => {
        aulaIdActual = null;
    });
}

if (btnAgregarEquipo) {
    btnAgregarEquipo.addEventListener("click", abrirAltaEquipo);
}

if (btnCerrarAgregarEquipo) {
    btnCerrarAgregarEquipo.addEventListener("click", cerrarEquipo);
}

if (dialogAgregarEquipo) {
    dialogAgregarEquipo.addEventListener("cancel", (evento) => {
        evento.preventDefault();
        cerrarEquipo();
    });
}

if (formularioEquipo) {
    formularioEquipo.addEventListener("submit", gestionarEquipo);
}

if (btnAgregarPortatil) {
    btnAgregarPortatil.addEventListener("click", abrirAltaPortatil);
}

if (btnCerrarAgregarPortatil) {
    btnCerrarAgregarPortatil.addEventListener("click", cerrarPortatil);
}

if (dialogAgregarPortatil) {
    dialogAgregarPortatil.addEventListener("cancel", (evento) => {
        evento.preventDefault();
        cerrarPortatil();
    });
}

if (formularioPortatil) {
    formularioPortatil.addEventListener("submit", gestionarPortatil);
}

if (cuerpoTablaAulas) {
    actualizarTablaAulas();
}

if (cuerpoTablaPortatiles) {
    actualizarTablaPortatiles();
}