let aulasDisponibles = null;

async function cargarAulas() {
    if (aulasDisponibles !== null) return;

    const respuesta = await fetch("obtener_aulas.php");
    aulasDisponibles = await respuesta.json();
}

async function actualizarNumeros() {
    await cargarAulas();

    const tipo = document.getElementById("tipo").value;
    const selectNumero = document.getElementById("numero");

    selectNumero.innerHTML = '<option value="">Seleccionar</option>';

    const numeros = aulasDisponibles[tipo] || [];

    for (const numero of numeros) {
        const option = document.createElement("option");
        option.value = numero;
        option.textContent = numero;
        selectNumero.appendChild(option);
    }
}