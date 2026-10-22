
(function () {
    const API_METRICAS = "../index.php?recurso=metricas";

    const mensajeMetricas = document.getElementById("mensajeMetricas");
    const mensajeSinFallas = document.getElementById("mensajeSinFallas");
    const botonesPeriodo = document.querySelectorAll(
        ".filtroPeriodo button[data-periodo]"
    );

    let periodoActual = "todo";
    let numeroConsulta = 0;
    let graficoFallas = null;

    function mostrarMensaje(mensaje) {
        mensajeMetricas.textContent = mensaje;
        mensajeMetricas.className = "mensajeMetricas error";
        mensajeMetricas.hidden = false;
    }

    function ocultarMensaje() {
        mensajeMetricas.textContent = "";
        mensajeMetricas.hidden = true;
    }

    function manejarError(error) {
        if (error.status === 401 || error.status === 403) {
            sessionStorage.setItem(
                "errorAcceso",
                error.message
            );

            window.location.replace("./login.html");
            return;
        }

        mostrarMensaje(error.message);
    }

    async function leerRespuestaAPI(respuesta) {
        let resultado;

        try {
            resultado = await respuesta.json();
        } catch {
            const error = new Error(
                "El servidor no devolvió una respuesta JSON válida."
            );

            error.status = respuesta.status;
            throw error;
        }

        if (!respuesta.ok) {
            const error = new Error(
                resultado.mensaje ??
                "No se pudieron obtener las métricas."
            );

            error.status = respuesta.status;
            throw error;
        }

        if (!resultado.datos?.metricas) {
            throw new Error(
                "La respuesta no contiene las métricas esperadas."
            );
        }

        return resultado.datos.metricas;
    }

    
async function obtenerMetricas(periodo) {
    const parametros = new URLSearchParams({
        ruta: "metricas",
        recurso: "dashboard",
        periodo: periodo
    });

    const url = "../index.php?" + parametros.toString();

    console.log("URL consultada:", new URL(url, window.location.href).href);

    const respuesta = await fetch(url, {
        method: "GET",
        cache: "no-store",
        credentials: "same-origin"
    });

    return await leerRespuestaAPI(respuesta);
}


    function ponerTexto(id, valor) {
        const elemento = document.getElementById(id);

        if (!elemento) {
            return;
        }

        elemento.textContent =
            valor === null || valor === undefined
                ? "—"
                : String(valor);
    }

    function formatearNombre(valor) {
        if (!valor) {
            return "Sin especificar";
        }

        const nombres = {
            bajo_rendimiento: "Bajo rendimiento",
            falta_mouse: "Falta mouse",
            falta_teclado: "Falta teclado",
            mouse_no_funciona: "Mouse no funciona",
            teclado_no_funciona: "Teclado no funciona"
        };

        const clave = String(valor).toLowerCase();

        if (nombres[clave]) {
            return nombres[clave];
        }

        const nombre = String(valor)
            .replace(/_/g, " ")
            .trim();

        return nombre.charAt(0).toUpperCase() +
            nombre.slice(1);
    }

    function nombreAula(tipo, numero) {
        const tipoFormateado = formatearNombre(tipo);

        return tipoFormateado + " " + (numero ?? "");
    }

    function pintarContadores(metricas) {
        ponerTexto(
            "valorTotalRegistros",
            metricas.totalRegistros
        );

        ponerTexto(
            "valorTotalTickets",
            metricas.totalTickets
        );

        ponerTexto(
            "valorTotalFinalizados",
            metricas.totalFinalizados
        );

        ponerTexto(
            "valorTotalPendientes",
            metricas.totalPendientes
        );

        ponerTexto(
            "valorTotalEnProceso",
            metricas.totalEnProceso
        );

        ponerTexto(
            "valorPortatilesPrestadas",
            metricas.totalPortatilesPrestadas
        );

        ponerTexto(
            "valorBajoRendimiento",
            metricas.totalBajoRendimiento
        );
    }

    function crearCelda(valor) {
        const celda = document.createElement("td");

        celda.textContent =
            valor === null || valor === undefined
                ? "—"
                : String(valor);

        return celda;
    }

    function mostrarTablaVacia(cuerpo, columnas) {
        const fila = document.createElement("tr");
        const celda = document.createElement("td");

        celda.colSpan = columnas;
        celda.textContent =
            "No hay reportes para el período seleccionado.";

        fila.appendChild(celda);
        cuerpo.appendChild(fila);
    }

    function pintarTopPcs(pcs) {
        const cuerpo = document.getElementById("tablaTopPcs");

        cuerpo.replaceChildren();

        if (!Array.isArray(pcs) || pcs.length === 0) {
            mostrarTablaVacia(cuerpo, 3);
            return;
        }

        for (const pc of pcs) {
            const fila = document.createElement("tr");

            fila.appendChild(
                crearCelda(pc.PcNumPc)
            );

            fila.appendChild(
                crearCelda(
                    nombreAula(pc.AulaTipo, pc.AulaNumero)
                )
            );

            fila.appendChild(
                crearCelda(pc.CantidadReportes)
            );

            cuerpo.appendChild(fila);
        }
    }

    function pintarTopAulas(aulas) {
        const cuerpo = document.getElementById("tablaTopAulas");

        cuerpo.replaceChildren();

        if (!Array.isArray(aulas) || aulas.length === 0) {
            mostrarTablaVacia(cuerpo, 2);
            return;
        }

        for (const aula of aulas) {
            const fila = document.createElement("tr");

            fila.appendChild(
                crearCelda(
                    nombreAula(aula.Tipo, aula.Numero)
                )
            );

            fila.appendChild(
                crearCelda(aula.CantidadTickets)
            );

            cuerpo.appendChild(fila);
        }
    }

    function pintarPerifericos(metricas) {
        const pcFaltantes = metricas.pcMasVandalizada;

        if (pcFaltantes) {
            ponerTexto(
                "pcMasVandalizada",
                pcFaltantes.PcNumPc +
                " (" +
                nombreAula(
                    pcFaltantes.AulaTipo,
                    pcFaltantes.AulaNumero
                ) +
                ") — " +
                pcFaltantes.CantidadVandalismo +
                " reportes"
            );
        } else {
            ponerTexto(
                "pcMasVandalizada",
                "Sin reportes de periféricos faltantes."
            );
        }

        const lista = document.getElementById(
            "listaPcsSospechosas"
        );

        lista.replaceChildren();

        const pcs = Array.isArray(metricas.pcsSospechosas)
            ? metricas.pcsSospechosas
            : [];

        if (pcs.length === 0) {
            const elemento = document.createElement("li");

            elemento.textContent =
                "No hay equipos con tres o más reportes.";

            lista.appendChild(elemento);
        } else {
            for (const pc of pcs) {
                const elemento = document.createElement("li");

                elemento.textContent =
                    pc.PcNumPc +
                    " — " +
                    nombreAula(
                        pc.AulaTipo,
                        pc.AulaNumero
                    ) +
                    " (" +
                    pc.CantidadVandalismo +
                    " reportes)";

                lista.appendChild(elemento);
            }
        }

        const mouse = metricas.modeloMouseMasDanado;

        ponerTexto(
            "modeloMouseMasDanado",
            mouse
                ? mouse.Modelo +
                  " — " +
                  mouse.CantidadReportes +
                  " reportes"
                : "Sin reportes."
        );

        const teclado = metricas.modeloTecladoMasDanado;

        ponerTexto(
            "modeloTecladoMasDanado",
            teclado
                ? teclado.Modelo +
                  " — " +
                  teclado.CantidadReportes +
                  " reportes"
                : "Sin reportes."
        );
    }

    function pintarGraficoFallas(fallas) {
        const canvas = document.getElementById("graficoFallas");

        if (graficoFallas !== null) {
            graficoFallas.destroy();
            graficoFallas = null;
        }

        const datos = Array.isArray(fallas) ? fallas : [];

        if (datos.length === 0) {
            canvas.hidden = true;
            mensajeSinFallas.hidden = false;
            return;
        }

        if (typeof Chart === "undefined") {
            canvas.hidden = true;
            mensajeSinFallas.hidden = true;

            mostrarMensaje(
                "No se pudo cargar la biblioteca Chart.js. " +
                "Verificá la conexión a internet."
            );

            return;
        }

        canvas.hidden = false;
        mensajeSinFallas.hidden = true;

        const etiquetas = datos.map(function (falla) {
            return formatearNombre(falla.Fallo);
        });

        const cantidades = datos.map(function (falla) {
            return Number(falla.CantidadReportes);
        });

        graficoFallas = new Chart(canvas, {//grafico de incidentes comunes
            type: "bar",

            data: {
                labels: etiquetas,

                datasets: [
                    {
                        label: "Cantidad de reportes",
                        data: cantidades,
                        backgroundColor: "#4169E1",
                        borderColor: "#2745A3",
                        borderWidth: 1,
                        borderRadius: 5
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function (contexto) {
                                return (
                                    "Reportes: " +
                                    contexto.parsed.y
                                );
                            }
                        }
                    }
                },

                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    function pintarMetricas(metricas) {
        pintarContadores(metricas);

        pintarTopPcs(
            metricas.topPcsReportadas
        );

        pintarTopAulas(
            metricas.topAulasConIncidencias
        );

        pintarPerifericos(metricas);

        pintarGraficoFallas(
            metricas.fallasFrecuentes
        );
    }

    function actualizarBotones() {
        for (const boton of botonesPeriodo) {
            const activo =
                boton.dataset.periodo === periodoActual;

            boton.classList.toggle("activo", activo);
            boton.setAttribute(
                "aria-pressed",
                String(activo)
            );
        }
    }

    async function cargarMetricas() {
        const consultaActual = ++numeroConsulta;

        ocultarMensaje();

        try {
            const metricas = await obtenerMetricas(
                periodoActual
            );

            if (consultaActual !== numeroConsulta) {
                return;
            }

            pintarMetricas(metricas);

        } catch (error) {
            if (consultaActual === numeroConsulta) {
                manejarError(error);
            }
        }
    }

    for (const boton of botonesPeriodo) {
        boton.addEventListener("click", function () {
            periodoActual = boton.dataset.periodo;

            actualizarBotones();
            cargarMetricas();
        });
    }

    actualizarBotones();
    cargarMetricas();
})();
