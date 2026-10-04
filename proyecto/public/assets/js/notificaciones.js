document.addEventListener('DOMContentLoaded', () => {
    // 1. Manejo del Dialog de Notificaciones en el Navbar
    const btnCampana = document.getElementById('btnCampanaNotif');
    const dialogNotif = document.getElementById('dialogNotificaciones');
    const btnCerrarDialogNotif = document.getElementById('btnCerrarDialog');

    if (btnCampana && dialogNotif) {
        btnCampana.addEventListener('click', () => {
            dialogNotif.showModal();
        });

        if (btnCerrarDialogNotif) {
            btnCerrarDialogNotif.addEventListener('click', () => {
                dialogNotif.close();
            });
        }

        // Cerrar haciendo clic fuera del modal
        dialogNotif.addEventListener('click', (e) => {
            if (e.target === dialogNotif) {
                dialogNotif.close();
            }
        });
    }

    // 2. Manejo del Dialog de Diagnóstico Técnico en Detalle Registro
    const dialogDiag = document.getElementById('dialogDiagnostico');
    const textoDiag = document.getElementById('textoDiagnostico');
    const btnCerrarDiag = document.getElementById('btnCerrarDiagnostico');
    const botonesDiagnostico = document.querySelectorAll('.btnDiagnostico');

    if (dialogDiag && textoDiag) {
        botonesDiagnostico.forEach(btn => {
            btn.addEventListener('click', function () {
                const mensajeDiagnostico = this.getAttribute('data-diagnostico');
                textoDiag.textContent = mensajeDiagnostico || 'Sin diagnóstico registrado.';
                dialogDiag.showModal();
            });
        });

        if (btnCerrarDiag) {
            btnCerrarDiag.addEventListener('click', () => {
                dialogDiag.close();
            });
        }

        // Cerrar haciendo clic fuera del modal
        dialogDiag.addEventListener('click', (e) => {
            if (e.target === dialogDiag) {
                dialogDiag.close();
            }
        });
    }
});