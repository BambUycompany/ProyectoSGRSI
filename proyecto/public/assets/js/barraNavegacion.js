(function () {
    const API_LOGOUT = "../index.php?ruta=logout";
    const PAGINAS_POR_ROL = {
        administrador: "./administrador.html",
        soporte: "./soporte.html",
        solicitante: "./solicitante.html"
    };

    const rolActivo = sessionStorage.getItem("rolActivo");
    const roles = JSON.parse(sessionStorage.getItem("roles") ?? "[]");

    const enlaceInicio = document.getElementById("enlaceInicio");
    if (enlaceInicio) {
        enlaceInicio.href = PAGINAS_POR_ROL[rolActivo] ?? "./login.html";
    }

    for (const elemento of document.querySelectorAll("[data-roles]")) {
        if (!elemento.dataset.roles.split(" ").includes(rolActivo)) {
            elemento.style.display = "none";
            for (const campo of elemento.querySelectorAll("input, select, textarea, button")) {
                campo.disabled = true;
            }
        }
    }

    const saludo = document.getElementById("saludoBienvenida");
    if (saludo) {
        const rolTexto = rolActivo ? rolActivo.charAt(0).toUpperCase() + rolActivo.slice(1) : "";
        saludo.textContent = `Bienvenido ${rolTexto}, ${sessionStorage.getItem("nombre") ?? ""} ${sessionStorage.getItem("apellido") ?? ""}`;
    }

    const itemCambiarRol = document.getElementById("itemCambiarRol");
    const btnCambiarRol = document.getElementById("btnCambiarRol");
    if (itemCambiarRol && roles.length > 1) {
        itemCambiarRol.style.display = "";
    }
    if (btnCambiarRol) {
        btnCambiarRol.addEventListener("click", () => window.location.replace("./seleccion_dashboard.html"));
    }

    const btnCerrarSesion = document.getElementById("btnCerrarSesion");
    if (btnCerrarSesion) {
        btnCerrarSesion.addEventListener("click", async () => {
            try {
                btnCerrarSesion.disabled = true;
                const respuesta = await fetch(API_LOGOUT, {
                    method: "POST",
                    headers: { "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? "" }
                });
                if (!respuesta.ok) {
                    throw new Error("No se pudo cerrar la sesión.");
                }
                sessionStorage.clear();
                window.location.replace("./login.html");
            } catch (error) {
                window.alert(error.message);
            } finally {
                btnCerrarSesion.disabled = false;
            }
        });
    }
})();