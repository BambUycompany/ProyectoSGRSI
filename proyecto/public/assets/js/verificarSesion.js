(function () {
    const rolActivo = sessionStorage.getItem("rolActivo");
    const roles = JSON.parse(sessionStorage.getItem("roles") ?? "[]");
    const permitidos = (document.body.dataset.rolesPermitidos ?? "").split(" ").filter(Boolean);

    if (!sessionStorage.getItem("cedula")) {
        window.location.replace("./login.html");
        return;
    }

    if (permitidos.length > 0 && !permitidos.includes(rolActivo)) {
        window.location.replace(roles.length > 1 ? "./seleccion_dashboard.html" : "./login.html");
    }
})();