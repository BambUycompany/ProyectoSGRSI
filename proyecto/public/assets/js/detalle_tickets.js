const dialogPrioridad = document.getElementById("dlgPrioridad");
const inputPrioridadTicketId = document.getElementById("prioridad_ticket_id");
const selectPrioridad = document.getElementById("selectPrioridad");

// Referencias a modales y botones de Estado
const dialogEstado = document.getElementById("dlgEstado");
const inputEstadoTicketId = document.getElementById("estado_ticket_id");
const selectEstado = document.getElementById("selectEstado");

// Referencias a modales y botones de Finalizar Ticket
const dialogFinalizar = document.getElementById("dlgFinalizar");
const inputFinalizarTicketId = document.getElementById("finalizar_ticket_id");
const txtDiagnostico = document.getElementById("txtDiagnostico");

// Event listeners para abrir modal de Cambiar Prioridad
document.querySelectorAll(".btnPrioridad").forEach(boton => {
    boton.addEventListener("click", (e) => {
        const id = e.currentTarget.getAttribute("data-id");
        const prioridad = e.currentTarget.getAttribute("data-prioridad");

        inputPrioridadTicketId.value = id;
        if (prioridad) {
            selectPrioridad.value = prioridad;
        }

        dialogPrioridad.showModal();
    });
});

// Event listeners para abrir modal de Cambiar Estado
document.querySelectorAll(".btnEstado").forEach(boton => {
    boton.addEventListener("click", (e) => {
        const id = e.currentTarget.getAttribute("data-id");
        const estado = e.currentTarget.getAttribute("data-estado");

        inputEstadoTicketId.value = id;
        if (estado) {
            selectEstado.value = estado;
        }

        dialogEstado.showModal();
    });
});

// Event listeners para abrir modal de Finalizar Ticket
document.querySelectorAll(".btnFinalizar").forEach(boton => {
    boton.addEventListener("click", (e) => {
        const id = e.currentTarget.getAttribute("data-id");

        inputFinalizarTicketId.value = id;
        txtDiagnostico.value = "";

        dialogFinalizar.showModal();
    });
});

// Manejo de botones de cierre/cancelar en todos los modales
document.querySelectorAll(".btnCancelarModal").forEach(boton => {
    boton.addEventListener("click", () => {
        const dialog = boton.closest("dialog");
        if (dialog) {
            dialog.close();
        }
    });
});