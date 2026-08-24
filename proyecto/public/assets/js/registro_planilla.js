const btnCrearTicket = document.getElementById("btnCrearTicket");
const btnCerrarModalTicket = document.getElementById("btnCerrarModalTicket");
const creacionTicket = document.getElementById("creacionTicket");
const btnAgregarTicketALaLista = document.getElementById("btnAgregarTicketALaLista");
const hiddenInputsTickets = document.getElementById("hiddenInputsTickets");
const resumenTickets = document.getElementById("resumenTickets");

const inputNumeroPc = document.getElementById("numeroPc");
const inputFallo = document.getElementById("fallo");
const inputDescripcion = document.getElementById("descripcion");

let contadorTickets = 0;
const ticketsAgregados = [];

function abrirModalTicket() {
    creacionTicket.style.display = "block";
}

function cerrarModalTicket() {
    creacionTicket.style.display = "none";
    inputNumeroPc.value = "";
    inputFallo.value = "";
    inputDescripcion.value = "";
}

function crearInputOculto(nombre, valor) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = nombre;
    input.value = valor;
    return input;
}

function agregarTicketALaLista() {
    if (!inputNumeroPc.checkValidity() || !inputFallo.checkValidity() || !inputDescripcion.checkValidity()) {
        alert("Completa todos los campos correctamente antes de agregar el ticket.");
        return;
    }

    const idTemporal = contadorTickets++;

    const ticket = {
        idTemporal,
        numeroPc: inputNumeroPc.value.trim(),
        fallo: inputFallo.value,
        descripcion: inputDescripcion.value.trim()
    };

    ticketsAgregados.push(ticket);

    hiddenInputsTickets.appendChild(crearInputOculto(`tickets[${idTemporal}][numeroPc]`, ticket.numeroPc));
    hiddenInputsTickets.appendChild(crearInputOculto(`tickets[${idTemporal}][fallo]`, ticket.fallo));
    hiddenInputsTickets.appendChild(crearInputOculto(`tickets[${idTemporal}][descripcion]`, ticket.descripcion));

    renderizarResumen();
    cerrarModalTicket();
}

function quitarTicket(idTemporal) {
    const indice = ticketsAgregados.findIndex(t => t.idTemporal === idTemporal);
    if (indice !== -1) ticketsAgregados.splice(indice, 1);

    document.querySelectorAll(`input[name^="tickets[${idTemporal}]"]`).forEach(input => input.remove()); 

    renderizarResumen();
}

function renderizarResumen() {
    resumenTickets.replaceChildren();

    if (ticketsAgregados.length === 0) return;

    const titulo = document.createElement("h3");
    titulo.textContent = "Fallas reportadas en este registro:";
    resumenTickets.appendChild(titulo);

    for (const ticket of ticketsAgregados) {
        const item = document.createElement("div");
        item.classList.add("itemTicketResumen");
        item.textContent = `${ticket.numeroPc} — ${ticket.fallo}`;

        const btnQuitar = document.createElement("button");
        btnQuitar.type = "button";
        btnQuitar.textContent = "Quitar";
        btnQuitar.addEventListener("click", () => quitarTicket(ticket.idTemporal));

        item.appendChild(btnQuitar);
        resumenTickets.appendChild(item);
    }
}

btnCrearTicket.addEventListener("click", abrirModalTicket);
btnCerrarModalTicket.addEventListener("click", cerrarModalTicket);
btnAgregarTicketALaLista.addEventListener("click", agregarTicketALaLista);