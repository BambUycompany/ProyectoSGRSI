
(function () {
    const cedula = sessionStorage.getItem("cedula");
    const rolActivo = sessionStorage.getItem("rolActivo");

    let roles = [];

    try {
        roles = JSON.parse(sessionStorage.getItem("roles") ?? "[]");
    } catch {
        roles = [];
    }

    const permitidos = (
        document.body.dataset.rolesPermitidos ?? ""
    ).split(/\s+/).filter(Boolean);

    function denegarAcceso(mensaje) {
        sessionStorage.setItem("errorAcceso", mensaje);
        window.location.replace("./login.html");
    }

    if (!cedula || !sessionStorage.getItem("csrfToken")) {
        denegarAcceso("Tu sesión no está iniciada o ha vencido. Iniciá sesión nuevamente.");
        return;
    }

    if (!Array.isArray(roles) || !roles.includes(rolActivo)) {
        denegarAcceso("El rol activo no es válido para esta sesión.");
        return;
    }

    if (permitidos.length > 0 && !permitidos.includes(rolActivo)) {
        denegarAcceso("No tenés permiso para acceder a esta página con tu rol actual.");
        return;
    }
})();
