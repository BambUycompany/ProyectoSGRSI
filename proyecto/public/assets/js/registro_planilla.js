
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

async function abrirModalTicket() {
    const tipo = document.getElementById("tipo").value;
    const numero = document.getElementById("numero").value;

    if (!tipo || !numero) {
        await mostrarToast(
            "Primero seleccioná una sala.",
            "advertencia"
        );
        return;
    }

    creacionTicket.style.display = "block";
}

async function cerrarModalTicket() {
    creacionTicket.style.display = "none";

    inputNumeroPc.value = "";
    inputFallo.value = "";
    inputDescripcion.value = "";
}


async function agregarTicketALaLista() {
    const numeroIngresado = inputNumeroPc.value.trim();
    const fallo = inputFallo.value;
    const descripcion = inputDescripcion.value.trim();

    const pcs = window.pcsDelAulaActual ?? [];

    const numeroPc = pcs.find(
        pc => String(pc).trim().toLowerCase() ===
              numeroIngresado.toLowerCase()
    );

    if (!numeroPc) {
        await mostrarToast(
            "La PC indicada no existe en el aula seleccionada.",
            "advertencia"
        );
        return;
    }

    if (!inputFallo.checkValidity() || !descripcion) {
        await mostrarToast(
            "Completá todos los datos de la falla.",
            "advertencia"
        );
        return;
    }

    const ticket = {
        idTemporal: contadorTickets++,
        numeroPc: numeroPc,
        fallo,
        descripcion
    };

    ticketsAgregados.push(ticket);

    await renderizarResumen();
    await cerrarModalTicket();

    await mostrarToast(
        "Ticket agregado al registro.",
        "exito"
    );
}


async function quitarTicket(idTemporal) {
    let indice = -1;

    for (let i = 0; i < ticketsAgregados.length; i++) {
        if (ticketsAgregados[i].idTemporal === idTemporal) {
            indice = i;
            break;
        }
    }

    if (indice !== -1) {
        ticketsAgregados.splice(indice, 1);
    }

    await renderizarResumen();
}

async function quitarTicketDesdeBoton(evento) {
    const boton = evento.currentTarget;
    const idTemporal = Number(boton.dataset.idTemporal);

    await quitarTicket(idTemporal);
}

async function renderizarResumen() {
    if (!resumenTickets) return;

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
        btnQuitar.dataset.idTemporal = ticket.idTemporal;

        btnQuitar.addEventListener(
            "click",
            quitarTicketDesdeBoton
        );

        item.appendChild(descripcion);
        item.appendChild(btnQuitar);

        resumenTickets.appendChild(item);
    }
}

async function limpiarTickets() {
    ticketsAgregados.length = 0;
    contadorTickets = 0;

    await renderizarResumen();
}

async function obtenerTicketsDelFormulario() {
    const tickets = [];

    for (const ticket of ticketsAgregados) {
        tickets.push({
            numeroPc: ticket.numeroPc,
            fallo: ticket.fallo,
            descripcion: ticket.descripcion
        });
    }

    return tickets;
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
