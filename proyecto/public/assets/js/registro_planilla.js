
const btnCrearTicket = document.getElementById("btnCrearTicket");
const btnCerrarModalTicket = document.getElementById("btnCerrarModalTicket");
const creacionTicket = document.getElementById("creacionTicket");
const btnAgregarTicketALaLista = document.getElementById(
    "btnAgregarTicketALaLista"
);
const resumenTickets = document.getElementById("resumenTickets");

const inputNumeroPc = document.getElementById("numeroPc");
const inputFallo = document.getElementById("fallo");
const inputDescripcion = document.getElementById("descripcion");


const ticketsAgregados = [];
let contadorTickets = 0;

function abrirModalTicket() {

    
    const tipo = document.getElementById("tipo").value;
    const numero = document.getElementById("numero").value;

    if (!tipo || !numero) {
        window.alert("Primero seleccioná una sala.");
        return;
    }

    creacionTicket.style.display = "block";
}

function cerrarModalTicket() {

    creacionTicket.style.display = "none";

    inputNumeroPc.value = "";
    inputFallo.value = "";
    inputDescripcion.value = "";
}

function agregarTicketALaLista() {

    const numeroPc = inputNumeroPc.value.trim().toUpperCase();
    const fallo = inputFallo.value;
    const descripcion = inputDescripcion.value.trim();

    const pcs = window.pcsDelAulaActual ?? [];

    if (!pcs.includes(numeroPc)) {
        window.alert(
            "La PC indicada no existe en el aula seleccionada."
        );
        return;
    }

    if (!inputFallo.checkValidity() || !descripcion) {
        window.alert("Completá todos los datos de la falla.");
        return;
    }

    const ticket = {
        idTemporal: contadorTickets++,
        numeroPc,
        fallo,
        descripcion
    };

    ticketsAgregados.push(ticket);

    renderizarResumen();
    cerrarModalTicket();
}

function quitarTicket(idTemporal) {

    const indice = ticketsAgregados.findIndex(
        ticket => ticket.idTemporal === idTemporal
    );

    if (indice !== -1) {
        ticketsAgregados.splice(indice, 1);
    }

    renderizarResumen();
}

function renderizarResumen() {

    resumenTickets.replaceChildren();

    if (ticketsAgregados.length === 0) {
        return;
    }

    const titulo = document.createElement("h3");
    titulo.textContent = "Fallas reportadas en este registro:";
    resumenTickets.appendChild(titulo);

    for (const ticket of ticketsAgregados) {

        const item = document.createElement("div");
        item.classList.add("itemTicketResumen");

        const descripcion = document.createElement("span");

        descripcion.textContent =
            `${ticket.numeroPc} — ${ticket.fallo}: ` +
            `${ticket.descripcion}`;

        const btnQuitar = document.createElement("button");
        btnQuitar.type = "button";
        btnQuitar.textContent = "Quitar";

        btnQuitar.addEventListener("click", () => {
            quitarTicket(ticket.idTemporal);
        });

        item.appendChild(descripcion);
        item.appendChild(btnQuitar);

        resumenTickets.appendChild(item);
    }
}


function limpiarTickets() {

    ticketsAgregados.length = 0;
    contadorTickets = 0;

    renderizarResumen();
}

function obtenerTicketsDelFormulario() {

    return ticketsAgregados.map(ticket => ({
        numeroPc: ticket.numeroPc,
        fallo: ticket.fallo,
        descripcion: ticket.descripcion
    }));
}

if (btnCrearTicket) {
    btnCrearTicket.addEventListener(
        "click",
        abrirModalTicket
    );
}

if (btnCerrarModalTicket) {
    btnCerrarModalTicket.addEventListener(
        "click",
        cerrarModalTicket
    );
}

if (btnAgregarTicketALaLista) {
    btnAgregarTicketALaLista.addEventListener(
        "click",
        agregarTicketALaLista
    );
}
