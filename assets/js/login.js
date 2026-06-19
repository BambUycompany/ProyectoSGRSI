const SEMILLA_EMPLEADOS = [
    { cedula: "1111", nombre: "Ana", apellido: "Admin", email: "admin@cetp.edu", rol: "administrador" },
    { cedula: "2222", nombre: "Sergio", apellido: "Soporte", email: "soporte@cetp.edu", rol: "soporte" },
    { cedula: "3333", nombre: "Sofia", apellido: "Solicitante", email: "solicitante@cetp.edu", rol: "solicitante" }
];

const formLogin = document.getElementById("formLogin");
const inputCedulaLogin = document.getElementById("cedula");

function cargarEmpleadosGuardadosLocal() {
    const empleadosGuardados = localStorage.getItem("empleados");
    if (empleadosGuardados === null) return [];
    return JSON.parse(empleadosGuardados);
}

function sembrarEmpleadosDePrueba() {
    if (localStorage.getItem("empleados") === null) {
        localStorage.setItem("empleados", JSON.stringify(SEMILLA_EMPLEADOS));
    }
}

function iniciarSesion(evento) {
    evento.preventDefault();

    const cedula = inputCedulaLogin.value.trim();
    const empleados = cargarEmpleadosGuardadosLocal();
    const usuario = empleados.find(emp => emp.cedula === cedula);

    if (usuario === undefined) {
        alert("Cédula no encontrada. Verifica que el usuario esté registrado.");
        return;
    }

    sessionStorage.setItem("rolActual", usuario.rol);
    sessionStorage.setItem("usuarioActual", usuario.nombre + " " + usuario.apellido);
    window.location.href = "index.html";
}

sembrarEmpleadosDePrueba();
formLogin.addEventListener("submit", iniciarSesion);
