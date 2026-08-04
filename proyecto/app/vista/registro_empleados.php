<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empleados</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/registroEmpleadosCSS.css">
</head>
<body>
    <header class="barraNav">
        <nav>
            <button class="btnMenu" id="btnMenu" type="button">
                <i class="bi bi-list"></i>
            </button>
            <button class="btnCerrarMenu" id="btnCerrarMenu" type="button">
                <i class="bi bi-list"></i>
            </button>

             <h1><a href="index.php"><img src="../assets\img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo " class="logo"> S.G.R.S.I </a></h1>
            <ul class="listaNavegacion">
               
                <li><a href="registro_sala.php" class="botones">Registro Sala</a></li>
                <li><a href="prestamos.php" class="botones">Prestamos</a></li>
                <li><a href="visualizar_solicitudes.php" class="botones">Solicitudes</a></li>
                <li><a href="login.php" class="botones"><i class="bi bi-person-fill"></i></a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="seccionTablaEmpleados">
            <div class="cabeceraTabla">
                <h2>Registro de Empleados</h2>
                <button type="button" class="btnOperacion" id="btnAgregarEmpleado">Agregar Empleado</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th id="colOperaciones">Operaciones</th>
                    </tr>
                </thead>
                <tbody id="listadoTablaEmpleados">
                </tbody>
            </table>
        </section>

        <dialog class="dialogAgregarEmpleado" >
            <button type="button" class="btnCerrarModal" id="btnCerrarAgregarEmpleado">x</button>
            <form action="registro_empleados.php" method="post" id="formAgregarEmpleado">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" maxlength="50" required>

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" maxlength="50" required>
                <label for="cedula">Cedula:</label>
                <input type="text" id="cedula" name="cedula" pattern="[0-9]{8}" maxlength="8" inputmode="numeric" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" maxlength="100" required>w
                
                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required>
                    <option value="">Seleccione un rol</option>
                    <option value="administrador">Administrador</option>
                    <option value="solicitante">Solicitante</option>
                    <option value="soporte">Soporte Técnico</option>
                </select>

                <button type="submit">Agregar</button>
            </form>
        </dialog>
    </main>

    <script src="../assets/js/navbar_responsive.js"></script>
    <script src="../assets/js/registro_empleados.js"></script>

    
</body>
</html>