
(function () {
    "use strict";

    const API_LOGOUT = "../index.php?ruta=logout";

    const PAGINAS_POR_ROL = {
        administrador: "./administrador.html",
        soporte: "./soporte.html",
        solicitante: "./solicitante.html"
    };

    const OPCIONES_NAVEGACION = [
        {
            texto: "Registro Sala",
            pagina: "./registro_planilla.html",
            roles: ["administrador", "soporte", "solicitante"]
        },
        {
            texto: "Empleados",
            pagina: "./registro_empleados.html",
            roles: ["administrador"]
        },
        {
            texto: "Gestor de Recursos",
            pagina: "./gestor_recursos.html",
            roles: ["administrador"]
        },
        {
            texto: "Listado Registro",
            pagina: "./listado_registros.html",
            roles: ["soporte"]
        },
        {
            texto: "Tickets",
            pagina: "./listado_tickets.html",
            roles: ["soporte"]
        },
        {
            texto: "Métricas",
            pagina: "./metricas.html",
            roles: ["administrador"]
        },
        {
            texto: "Solicitar Préstamo",
            pagina: "./solicitar_prestamos.html",
            roles: ["solicitante"]
        },
        {
            texto: "Listado de Préstamos",
            pagina: "./listado_prestamo.html",
            roles: ["solicitante", "soporte"]
        }
    ];

    const rolActivo = sessionStorage.getItem("rolActivo");

    let roles = [];

    try {
        const rolesGuardados = JSON.parse(
            sessionStorage.getItem("roles") ?? "[]"
        );

        if (Array.isArray(rolesGuardados)) {
            roles = rolesGuardados;
        }
    } catch (error) {
        roles = [];
    }

    const btnMenu = document.getElementById("btnMenu");
    const btnCerrarMenu = document.getElementById("btnCerrarMenu");
    const listaNavegacion = document.querySelector(".listaNavegacion");

    const btnIconoUsuario = document.getElementById("btnIconoUsuario");
    const opcionesUsuario = document.getElementById("opcionesUsuario");

    const btnCambiarRol = document.getElementById("btnCambiarRol");
    const btnCerrarSesion = document.getElementById("btnCerrarSesion");

    const enlaceInicio = document.getElementById("enlaceInicio");
    const saludoBienvenida = document.getElementById("saludoBienvenida");

    function obtenerPaginaInicio() {
        return PAGINAS_POR_ROL[rolActivo] ?? "./login.html";
    }

    function configurarEnlaceInicio() {
        if (enlaceInicio) {
            enlaceInicio.href = obtenerPaginaInicio();
        }

        const enlaceLogo = document.querySelector(
            ".barraNav h1 a"
        );

        if (enlaceLogo) {
            enlaceLogo.href = obtenerPaginaInicio();
        }
    }

    function crearOpcionNavegacion(opcion) {
        const elemento = document.createElement("li");
        const enlace = document.createElement("a");

        elemento.dataset.roles = opcion.roles.join(" ");

        enlace.href = opcion.pagina;
        enlace.classList.add("botones");
        enlace.textContent = opcion.texto;

        elemento.appendChild(enlace);

        return elemento;
    }

    function configurarNavegacion() {
        if (!listaNavegacion) {
            return;
        }

        const menuUsuario = listaNavegacion.querySelector(
            ".menuUsuario"
        );

        const fragmento = document.createDocumentFragment();

        for (const opcion of OPCIONES_NAVEGACION) {
            if (!opcion.roles.includes(rolActivo)) {
                continue;
            }

            fragmento.appendChild(
                crearOpcionNavegacion(opcion)
            );
        }

        listaNavegacion.replaceChildren();

        listaNavegacion.appendChild(fragmento);

        if (menuUsuario) {
            listaNavegacion.appendChild(menuUsuario);
        }
    }

    function configurarSaludo() {
        if (!saludoBienvenida) {
            return;
        }

        const nombre = sessionStorage.getItem("nombre") ?? "";
        const apellido = sessionStorage.getItem("apellido") ?? "";

        const rolTexto = rolActivo
            ? rolActivo.charAt(0).toUpperCase() + rolActivo.slice(1)
            : "";

        saludoBienvenida.textContent =
            `Bienvenido ${rolTexto}, ${nombre} ${apellido}`.trim();
    }

    function abrirMenu() {
        if (listaNavegacion) {
            listaNavegacion.classList.add("visible");
        }

        if (btnCerrarMenu) {
            btnCerrarMenu.classList.add("visible");
        }

        if (btnMenu) {
            btnMenu.classList.add("oculto");
        }
    }

    function cerrarMenu() {
        if (listaNavegacion) {
            listaNavegacion.classList.remove("visible");
        }

        if (btnCerrarMenu) {
            btnCerrarMenu.classList.remove("visible");
        }

        if (btnMenu) {
            btnMenu.classList.remove("oculto");
        }
    }

    function configurarMenuResponsive() {
        if (btnMenu) {
            btnMenu.addEventListener("click", abrirMenu);
        }

        if (btnCerrarMenu) {
            btnCerrarMenu.addEventListener(
                "click",
                cerrarMenu
            );
        }
    }

    function cerrarOpcionesUsuario() {
        if (opcionesUsuario) {
            opcionesUsuario.classList.remove("visible");
        }
    }

    function configurarMenuUsuario() {
        if (!btnIconoUsuario || !opcionesUsuario) {
            return;
        }

        btnIconoUsuario.addEventListener("click", function (evento) {
            evento.stopPropagation();

            opcionesUsuario.classList.toggle("visible");
        });

        document.addEventListener("click", function (evento) {
            if (
                !opcionesUsuario.contains(evento.target) &&
                !btnIconoUsuario.contains(evento.target)
            ) {
                cerrarOpcionesUsuario();
            }
        });

        document.addEventListener("keydown", function (evento) {
            if (evento.key === "Escape") {
                cerrarOpcionesUsuario();
                cerrarMenu();
            }
        });
    }

    function configurarCambioRol() {
        if (!opcionesUsuario) {
            return;
        }

        let boton = btnCambiarRol;
        let item = document.getElementById("itemCambiarRol");

        if (roles.length <= 1) {
            if (item) {
                item.style.display = "none";
            } else if (boton) {
                boton.style.display = "none";
            }

            return;
        }

        if (!boton) {
            item = document.createElement("li");
            item.id = "itemCambiarRol";

            boton = document.createElement("button");
            boton.type = "button";
            boton.id = "btnCambiarRol";
            boton.textContent = "Cambiar de rol";

            item.appendChild(boton);

            const itemCerrarSesion = btnCerrarSesion
                ? btnCerrarSesion.closest("li")
                : null;

            if (itemCerrarSesion) {
                opcionesUsuario.insertBefore(
                    item,
                    itemCerrarSesion
                );
            } else {
                opcionesUsuario.appendChild(item);
            }
        }

        if (item) {
            item.style.display = "";
        }

        boton.style.display = "";

        boton.addEventListener("click", function () {
            window.location.replace(
                "./seleccion_dashboard.html"
            );
        });
    }

    function configurarCierreSesion() {
        if (!btnCerrarSesion) {
            return;
        }

        btnCerrarSesion.addEventListener(
            "click",
            async function () {
                if (btnCerrarSesion.disabled) {
                    return;
                }

                btnCerrarSesion.disabled = true;

                try {
                    const respuesta = await fetch(API_LOGOUT, {
                        method: "POST",
                        headers: {
                            "X-CSRF-Token":
                                sessionStorage.getItem("csrfToken") ?? ""
                        }
                    });

                    if (!respuesta.ok) {
                        let mensaje = "No se pudo cerrar la sesión.";

                        try {
                            const datos = await respuesta.json();

                            if (datos.mensaje) {
                                mensaje = datos.mensaje;
                            }
                        } catch (error) {
                        }

                        throw new Error(mensaje);
                    }

                    sessionStorage.clear();

                    window.location.replace("./login.html");

                } catch (error) {
                    window.alert(error.message);

                    btnCerrarSesion.disabled = false;
                }
            }
        );
    }

    configurarEnlaceInicio();
    configurarNavegacion();
    configurarSaludo();
    configurarMenuResponsive();
    configurarMenuUsuario();
    configurarCambioRol();
    configurarCierreSesion();
})();
