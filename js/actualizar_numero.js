function actualizarNumeros() {
         const tipo = document.getElementById("tipo").value;
            const numeroSelect = document.getElementById("numero");
            numeroSelect.innerHTML = ''; 

            if (tipo === "laboratorio") {
                for (let i = 1; i <= 6; i++) {
                    const option = document.createElement("option"); 
                    option.value = "laboratorio-" + i;
                option.textContent = "Laboratorio " + i;
                    numeroSelect.appendChild(option);
                }
            } else if (tipo === "taller") {
                for (let i = 1; i <= 3; i++) {
                    const option = document.createElement("option");
                    option.value = "taller-" + i; 
                    option.textContent = "Taller " + i; 
                    numeroSelect.appendChild(option);
                }
            } else {
                const option = document.createElement("option");
                option.textContent = "Seleccionar tipo primero";
                numeroSelect.appendChild(option); 
            }
        }