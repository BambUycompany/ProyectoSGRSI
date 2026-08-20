<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empleados</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/registroEmpleadosCSS.css">
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

             <h1><a href="administrador.php"><img src="../public/assets/img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo " class="logo"> S.G.R.S.I </a></h1>
            <ul class="listaNavegacion">
               
                <li data-roles="solicitante administrador soporte"><a href="registro_sala.html" class="botones">Registro Sala</a></li>
                <li data-roles="administrador soporte"><a href="metricas.html" class="botones">Métricas</a></li>
                <li data-roles="soporte"><a href="listado_tickets.html" class="botones">Tickets</a></li>
                <li class="menuUsuario">
                    <button type="button" id="btnIconoUsuario" class="botones"><i class="bi bi-person-fill"></i></button>
                    <ul class="opcionesUsuario" id="opcionesUsuario">
                        <li><button type="button" id="btnCerrarSesion">Cerrar sesión</button></li>
                    </ul>
                </li>
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
                        <th>Cedula</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Rol</th>
                        <th id="colOperacionesAcciones">Acciones</th>
                    </tr>
                <?php //Mejorar cosméticamente en un futuro este tipo de captura información con GET ?>
            <?= htmlspecialchars($_GET["error"] ?? "") ?>
            <?= htmlspecialchars($_GET["resultado"] ?? "") ?>
                </thead>

                <tbody id="cuerpoTablaEmpleados">
                    <?php foreach ($usuarios as $usuario) { ?>

                        <?php
                            $roles = "";

                            if ($usuario["administrador"] == 1) {
                                $roles = "Administrador";
                            }

                            if ($usuario["soporte"] == 1) {
                                if ($roles != "") {
                                    $roles = $roles . ", ";
                                }

                                $roles = $roles . "Soporte";
                            }
                            if ($usuario["solicitante"] == 1) {
                                if ($roles != "") {
                                    $roles = $roles . ", ";
                                }

                                $roles = $roles . "Solicitante";
                            }

                            if ($roles == "") {
                                $roles = "Sin rol";
                            }
                            /* <?php echo $variable ?> equivalente a <?= $variable ?>*/ 
                        ?>

                        <tr>
                            <td><?= htmlspecialchars($usuario["cedula"]) ?></td>
                            <td><?= htmlspecialchars($usuario["nombre"]) ?></td>
                            <td><?= htmlspecialchars($usuario["apellido"]) ?></td>
                            <td><?= htmlspecialchars($roles) ?></td>

                            <td>
                                <div class="cajaOperaciones">
                                    <button type="button" class="btnOperacion btnModificar">Modificar</button>
                                    
                                    <form action="../app/controlador/procesarBajaUsuario.php" method="post" class="formularioEliminarEmpleado">
                                        <input type="hidden" name="cedula" value="<?=htmlspecialchars($usuario["cedula"])?>">
                                        <input type="hidden" name="csrfToken" value="<?=htmlspecialchars($_SESSION["csrfToken"])?>">
                                        <button type="submit" class="btnOperacion" id="btnEliminar">Eliminar</button>
                                    </form>
                                    
                                    
                                </div>
                            </td>
                        </tr>

                    <?php } ?>
                </tbody>
          
       
            </table>
        </section>

        <dialog class="dialogAgregarEmpleado" >
           
            <button type="button" class="btnCerrarModal" id="btnCerrarAgregarEmpleado">x</button>
            <form action="../app/controlador/procesarAltaUsuario.php" method="post" id="formAgregarEmpleado">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" maxlength="50" required>

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" maxlength="50" required>
                <label for="cedula">Cedula:</label>
                <input type="text" id="cedula" name="cedula" pattern="[0-9]{8}" maxlength="8" inputmode="numeric" required>

                <label for="claveHash">Contraseña:</label>
                <input type="password" id="claveHash" name="claveHash" maxlength="100" required>

                <label for="confirmarClave">Confirmar Contraseña:</label>
                <input type="password" id="confirmarClave" name="confirmarClave" maxlength="100" required>

                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required>
                    <option value="">Seleccione un rol</option>
                    <option value="administrador">Administrador</option>
                    <option value="solicitante">Solicitante</option>
                    <option value="soporte">Soporte Tecnico</option>
                </select>

                <button type="submit">Agregar</button>
            </form>
        </dialog>
    </main>

    <script src="../public/assets/js/navbar_responsive.js"></script>
    <script src="../public/assets/js/registro_empleados.js"></script>

    
</body>
</html>