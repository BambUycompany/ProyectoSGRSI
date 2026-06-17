const validRoles = ['solicitante', 'administrador', 'soporte'];
const role = sessionStorage.getItem('rolActual');

// Si no hay una sesión válida, se vuelve al login.
if (!validRoles.includes(role)) {
    window.location.href = 'login.html';
} else {
    // Muestra la sección que corresponde al rol con el que se inició sesión.
    document.getElementById(role).style.display = 'block';

    // "Agregar usuario" en el navbar solo se muestra para el administrador.
    const navAgregarUsuario = document.getElementById('navAgregarUsuario');
    if (navAgregarUsuario !== null) {
        navAgregarUsuario.style.display = role === 'administrador' ? '' : 'none';
    }

    // El icono de usuario despliega/oculta el menú con la opción de cerrar sesión.
    const btnIconoUsuario = document.getElementById('btnIconoUsuario');
    const opcionesUsuario = document.getElementById('opcionesUsuario');
    if (btnIconoUsuario !== null && opcionesUsuario !== null) {
        btnIconoUsuario.addEventListener('click', (evento) => {
            evento.stopPropagation();
            opcionesUsuario.classList.toggle('visible');
        });

        // Si se hace click fuera del menú, se cierra.
        document.addEventListener('click', () => {
            opcionesUsuario.classList.remove('visible');
        });
    }

    // Cerrar sesión: limpia la sesión y vuelve al login.
    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    if (btnCerrarSesion !== null) {
        btnCerrarSesion.addEventListener('click', () => {
            sessionStorage.clear();
            window.location.href = 'login.html';
        });
    }
}
