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

const aulasPorId = new Map();
const equiposPorNumero = new Map();
const portatilesPorId = new Map();

async function alClickDetalleAula(evento) {
    evento.preventDefault();
    await abrirDetalleAula(evento.currentTarget.dataset.aulaId);
}

async function alClickModificarAula(evento) {
    const aula = aulasPorId.get(evento.currentTarget.dataset.aulaId);
    if (aula) await abrirModificarAula(aula);
}

async function alClickEliminarAula(evento) {
    await eliminarAula(evento.currentTarget.dataset.aulaId);
}

async function alClickModificarEquipo(evento) {
    const equipo = equiposPorNumero.get(evento.currentTarget.dataset.numPc);
    if (equipo) await abrirModificarEquipo(equipo);
}

async function alClickEliminarEquipo(evento) {
    await eliminarEquipo(evento.currentTarget.dataset.numPc, aulaIdActual);
}

async function alClickModificarPortatil(evento) {
    const portatil = portatilesPorId.get(evento.currentTarget.dataset.portatilId);
    if (portatil) await abrirModificarPortatil(portatil);
}

async function alClickEliminarPortatil(evento) {
    await eliminarPortatil(evento.currentTarget.dataset.portatilId);
}

async function alClickCambiarEstadoPortatil(evento) {
    await cambiarEstadoPortatil(
        evento.currentTarget.dataset.portatilId,
        evento.currentTarget.dataset.accion
    );
}

async function alCancelarAula(evento) {
    evento.preventDefault();
    await cerrarAula();
}

async function alCancelarEquipo(evento) {
    evento.preventDefault();
    await cerrarEquipo();
}

async function alCancelarPortatil(evento) {
    evento.preventDefault();
    await cerrarPortatil();
}

async function alCerrarDetalleEquipos() {
    aulaIdActual = null;
}

async function alClickCerrarDetalleEquipos() {
    dialogEquiposAula.close();
}

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

async function cabecerasMutacion() {
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
        headers: await cabecerasMutacion(),
        body: JSON.stringify({ recurso: "aula", tipo, numero })
    });

    return await leerRespuestaAPI(respuesta);
}

async function modificarAula(aulaId, tipo, numero) {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=aula`, {
        method: "PUT",
        headers: await cabecerasMutacion(),
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
                headers: await cabecerasMutacion()
            }
        );

        await leerRespuestaAPI(respuesta);
        await actualizarTablaAulas();
        await mostrarToast("Aula eliminada correctamente.", "exito");
    } catch (error) {
        await mostrarToast(error.message, "error");
    }
}

async function agregarFilaAula(aula) {
    const fila = document.createElement("tr");

    const campoTipo = document.createElement("td");
    const linkAula = document.createElement("a");

    linkAula.href = "#";
    linkAula.textContent = aula.Tipo ?? aula.tipo;

    const idAula = String(aula.ID ?? aula.id);
    aulasPorId.set(idAula, aula);
    linkAula.dataset.aulaId = idAula;
    linkAula.addEventListener("click", alClickDetalleAula);

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
    btnModificar.dataset.aulaId = idAula;
    btnModificar.addEventListener("click", alClickModificarAula);

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.classList.add("btnOperacion");
    btnEliminar.dataset.aulaId = idAula;
    btnEliminar.addEventListener("click", alClickEliminarAula);

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
        await mostrarToast("No se pudieron cargar las aulas: " + error.message, "error");
    }
}

async function abrirAltaAula() {
    aulaEnEdicion = false;
    aulaIdEnEdicion = null;
    formularioAula.reset();
    dialogAgregarAula.showModal();
}

async function abrirModificarAula(aula) {
    aulaEnEdicion = true;
    aulaIdEnEdicion = aula.ID ?? aula.id;

    entradaTipoAula.value = (aula.Tipo ?? aula.tipo ?? "").toLowerCase();
    entradaNumeroAula.value = aula.Numero ?? aula.numero;

    dialogAgregarAula.showModal();
}

async function cerrarAula() {
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

        await cerrarAula();
        await actualizarTablaAulas();
        await mostrarToast("Aula guardada correctamente.", "exito");
    } catch (error) {
        await mostrarToast(error.message, "error");
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
        await mostrarToast(
            "No se pudo abrir el detalle del aula: " + error.message, "error"
        );
    }
}

async function agregarEquipo(datosEquipo) {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=equipo`, {
        method: "POST",
        headers: await cabecerasMutacion(),
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
        headers: await cabecerasMutacion(),
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
                headers: await cabecerasMutacion()
            }
        );

        await leerRespuestaAPI(respuesta);
        await abrirDetalleAula(aulaId);
        await mostrarToast("Equipo eliminado correctamente.", "exito");
    } catch (error) {
        await mostrarToast(error.message, "error");
    }
}

async function agregarFilaEquipo(equipo) {
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

    equiposPorNumero.set(String(numPc), equipo);
    btnModificar.dataset.numPc = String(numPc);
    btnModificar.addEventListener("click", alClickModificarEquipo);

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.classList.add("btnOperacion");

    btnEliminar.dataset.numPc = String(numPc);
    btnEliminar.addEventListener("click", alClickEliminarEquipo);

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

async function abrirAltaEquipo() {
    if (!aulaIdActual) {
        await mostrarToast("Seleccione un aula antes de agregar equipos.", "advertencia");
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

async function abrirModificarEquipo(equipo) {
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

async function cerrarEquipo() {
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
            await mostrarToast("Complete todos los campos del equipo.", "advertencia");
            return;
        }

        if (!equipoEnEdicion) {
            const cantidad = Number(entradaCantidadEquipos.value);

            if (
                !Number.isInteger(cantidad) ||
                cantidad < 1 ||
                cantidad > 100
            ) {
                await mostrarToast(
                    "Indique una cantidad entera entre 1 y 100.", "advertencia"
                );
                return;
            }

            datosEquipo.cantidad = cantidad;
            await agregarEquipo(datosEquipo);
        } else {
            datosEquipo.numPc = entradaNumPc.value.trim();

            if (!datosEquipo.numPc) {
                await mostrarToast("No se pudo identificar la PC.", "advertencia");
                return;
            }

            await modificarEquipo(datosEquipo);
        }

        await cerrarEquipo();
        await abrirDetalleAula(aulaIdActual);
        await mostrarToast("Equipo guardado correctamente.", "exito");
    } catch (error) {
        await mostrarToast(error.message, "error");
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
            headers: await cabecerasMutacion(),
            body: JSON.stringify({ modeloPortatil })
        }
    );

    return await leerRespuestaAPI(respuesta);
}

async function modificarPortatil(portatilId, modeloPortatil) {
    const respuesta = await fetch(`${API_RECURSOS}&identificador=portatil`, {
        method: "PUT",
        headers: await cabecerasMutacion(),
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
                headers: await cabecerasMutacion()
            }
        );

        await leerRespuestaAPI(respuesta);
        await actualizarTablaPortatiles();
        await mostrarToast("Operación realizada correctamente.", "exito");
    } catch (error) {
        await mostrarToast(error.message, "error");
    }
}

async function cambiarEstadoPortatil(portatilId, accion) {
    try {
        const respuesta = await fetch(`${API_RECURSOS}&identificador=portatil`, {
            method: "PATCH",
            headers: await cabecerasMutacion(),
            body: JSON.stringify({
                portatilId,
                accion
            })
        });

        await leerRespuestaAPI(respuesta);
        await actualizarTablaPortatiles();
        await mostrarToast("Operación realizada correctamente.", "exito");
    } catch (error) {
        await mostrarToast(error.message, "error");
    }
}

async function agregarFilaPortatil(portatil) {
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

    const idPortatil = String(id);
    portatilesPorId.set(idPortatil, portatil);
    btnModificar.dataset.portatilId = idPortatil;
    btnModificar.addEventListener("click", alClickModificarPortatil);

    caja.appendChild(btnModificar);

    if (estado === "disponible") {
        const btnEliminar = document.createElement("button");
        btnEliminar.type = "button";
        btnEliminar.textContent = "Eliminar";
        btnEliminar.classList.add("btnOperacion");

        btnEliminar.dataset.portatilId = idPortatil;
        btnEliminar.addEventListener("click", alClickEliminarPortatil);

        caja.appendChild(btnEliminar);

        const btnDeshabilitar = document.createElement("button");
        btnDeshabilitar.type = "button";
        btnDeshabilitar.textContent = "Deshabilitar";
        btnDeshabilitar.classList.add("btnOperacion");

        btnDeshabilitar.dataset.portatilId = idPortatil;
        btnDeshabilitar.dataset.accion = "deshabilitar";
        btnDeshabilitar.addEventListener("click", alClickCambiarEstadoPortatil);

        caja.appendChild(btnDeshabilitar);
    } else if (estado === "deshabilitado") {
        const btnHabilitar = document.createElement("button");
        btnHabilitar.type = "button";
        btnHabilitar.textContent = "Habilitar";
        btnHabilitar.classList.add("btnOperacion");

        btnHabilitar.dataset.portatilId = idPortatil;
        btnHabilitar.dataset.accion = "habilitar";
        btnHabilitar.addEventListener("click", alClickCambiarEstadoPortatil);

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
        await mostrarToast(
            "No se pudieron cargar los portátiles: " + error.message, "error"
        );
    }
}

async function abrirAltaPortatil() {
    portatilEnEdicion = false;
    portatilIdEnEdicion = null;

    formularioPortatil.reset();
    dialogAgregarPortatil.showModal();
}

async function abrirModificarPortatil(portatil) {
    portatilEnEdicion = true;
    portatilIdEnEdicion = portatil.id ?? portatil.ID;

    entradaModeloPortatil.value =
        portatil.modelo ?? portatil.Modelo;

    dialogAgregarPortatil.showModal();
}

async function cerrarPortatil() {
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

        await cerrarPortatil();
        await actualizarTablaPortatiles();
        await mostrarToast("Portátil guardado correctamente.", "exito");
    } catch (error) {
        await mostrarToast(error.message, "error");
    }
}



if (btnAgregarAula) {
    btnAgregarAula.addEventListener("click", abrirAltaAula);
}

if (btnCerrarAgregarAula) {
    btnCerrarAgregarAula.addEventListener("click", cerrarAula);
}

if (dialogAgregarAula) {
    dialogAgregarAula.addEventListener("cancel", alCancelarAula);
}

if (formularioAula) {
    formularioAula.addEventListener("submit", gestionarAula);
}

if (btnCerrarEquiposAula) {
    btnCerrarEquiposAula.addEventListener("click", alClickCerrarDetalleEquipos);
}

if (dialogEquiposAula) {
    dialogEquiposAula.addEventListener("close", alCerrarDetalleEquipos);
}

if (btnAgregarEquipo) {
    btnAgregarEquipo.addEventListener("click", abrirAltaEquipo);
}

if (btnCerrarAgregarEquipo) {
    btnCerrarAgregarEquipo.addEventListener("click", cerrarEquipo);
}

if (dialogAgregarEquipo) {
    dialogAgregarEquipo.addEventListener("cancel", alCancelarEquipo);
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
    dialogAgregarPortatil.addEventListener("cancel", alCancelarPortatil);
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