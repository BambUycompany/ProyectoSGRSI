
const API_AULAS =
    "../index.php?ruta=recursos&recurso=aula";

const selectTipo = document.getElementById("tipo");
const selectNumero = document.getElementById("numero");
const inputNumeroPcTicket = document.getElementById("numeroPc");

let aulasDisponibles = [];

window.pcsDelAulaActual = [];

async function leerRespuestaAulas(respuesta) {

    const texto = await respuesta.text();

    let json;

    try {
        json = JSON.parse(texto);
    } catch {
        throw new Error(
            "El servidor no devolvió JSON válido."
        );
    }

    if (!respuesta.ok) {
        throw new Error(
            json.mensaje ?? "Error al consultar las aulas."
        );
    }

    return json.datos;
}

async function cargarAulas() {

    try {
        const respuesta = await fetch(API_AULAS);

        const resultado = await leerRespuestaAulas(respuesta);

        aulasDisponibles = resultado.aulas ?? [];

        actualizarNumeros();

    } catch (error) {
        window.alert(
            "No se pudieron cargar las aulas: " +
            error.message
        );
    }
}

function actualizarNumeros() {

    const tipo = selectTipo.value;

    selectNumero.replaceChildren();

    const inicial = document.createElement("option");
    inicial.value = "";
    inicial.textContent = "Seleccionar";
    selectNumero.appendChild(inicial);

    window.pcsDelAulaActual = [];

    limpiarTickets();

    const aulas = aulasDisponibles.filter(
        aula => (aula.Tipo ?? "").toLowerCase() === tipo
    );

    for (const aula of aulas) {

        const opcion = document.createElement("option");

        opcion.value = aula.Numero;
        opcion.textContent = aula.Numero;

        opcion.dataset.aulaId = aula.ID;

        selectNumero.appendChild(opcion);
    }
}

async function cargarPcsDelAula() {

    window.pcsDelAulaActual = [];
    limpiarTickets();

    const opcion = selectNumero.selectedOptions[0];

    const aulaId = opcion?.dataset.aulaId;

    if (!aulaId) {
        return;
    }

    try {
        const respuesta = await fetch(
            `${API_AULAS}&aulaId=${encodeURIComponent(aulaId)}`
        );

        const resultado = await leerRespuestaAulas(respuesta);

        const equipos = resultado.equipos ?? [];

        window.pcsDelAulaActual = equipos.map(
            equipo => equipo.NumPc
        );

     
        if (inputNumeroPcTicket) {
            inputNumeroPcTicket.setAttribute(
                "list",
                "listaPcsDisponibles"
            );

            let lista = document.getElementById(
                "listaPcsDisponibles"
            );

            if (!lista) {
                lista = document.createElement("datalist");
                lista.id = "listaPcsDisponibles";
                document.body.appendChild(lista);
            }

            lista.replaceChildren();

            for (const pc of window.pcsDelAulaActual) {
                const opcionPc = document.createElement("option");
                opcionPc.value = pc;
                lista.appendChild(opcionPc);
            }
        }

    } catch (error) {

        window.alert(
            "No se pudieron cargar las PC: " +
            error.message
        );
    }
}

if (selectTipo && selectNumero) {

    selectTipo.addEventListener(
        "change",
        actualizarNumeros
    );

    selectNumero.addEventListener(
        "change",
        cargarPcsDelAula
    );

    cargarAulas();
}
