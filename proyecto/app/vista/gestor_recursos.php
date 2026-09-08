<?php
session_start();

// 1. Cargar las aulas desde el controlador/modelo antes de renderizar
require_once __DIR__ . "/../app/controlador/prepararObtenerAulas.php";

// Generar Token CSRF si no existe
if (empty($_SESSION["csrfToken"])) {
    $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de recursos</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/gestorRecursosCSS.css">

</head>
<body>
    <header>
        <nav>
            <button class="btnMenu" id="btnMenu" type="button">
                <i class="bi bi-list"></i>
            </button>
            <button class="btnCerrarMenu" id="btnCerrarMenu" type="button">
                <i class="bi bi-list"></i>
            </button>

             <h1><a href="administrador.php"><img src="../public/assets/img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo " class="logo"> S.G.R.S.I </a></h1>
            <ul class="listaNavegacion">
               
                <li data-roles="solicitante administrador soporte"><a href="registro_sala.php" class="botones">Registro Sala</a></li>
                <li data-roles="administrador soporte"><a href="metricas.php" class="botones">Métricas</a></li>
                <li data-roles="soporte"><a href="listado_tickets.php" class="botones">Tickets</a></li>
                <li class="menuUsuario">
                    <button type="button" id="btnIconoUsuario" class="botones"><i class="bi bi-person-fill"></i></button>
                    <ul class="opcionesUsuario" id="opcionesUsuario">
                        <?php if (isset($_SESSION["roles"]) && count($_SESSION["roles"]) > 1): ?>
                            <li><button type="button" id="btnCambiarRol">Cambiar de rol</button></li>
                        <?php endif; ?>
                        <li><button type="button" id="btnCerrarSesion">Cerrar sesión</button></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Gestor de recursos</h1>
        <?php if (isset($_GET["error"])): ?>
            <p style="color:red;"><?= htmlspecialchars($_GET["error"]) ?></p>
        <?php endif; ?>
        <?php if (isset($_GET["resultado"])): ?>
            <p style="color:green;"><?= htmlspecialchars($_GET["resultado"]) ?></p>
        <?php endif; ?>
        <section class="seccionGestorRecursos">
            <div class="cabeceraTabla">
                <h2>Gestor de Aulas</h2>
                <button type="button" class="btnOperacion" id="btnAgregarAula">Agregar Aula</button>
            </div>
            <?php if (count($aulas) === 0): ?>
                <p>No hay aulas registradas.</p>    
            <?php else: ?>    
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Número</th>
                        <th>Capacidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aulas as $aula): ?>
                        <tr>
                            <td><?= htmlspecialchars($aula['ID']) ?></td>
                            <td><a href="detalle_aula.php?aulaId=<?= urlencode($aula['ID']) ?>"> <?= htmlspecialchars($aula['Tipo']) ?> </a></td>
                            <td><?= htmlspecialchars($aula['Numero']) ?> </td>
                            <td><?= htmlspecialchars($aula['CantidadPcs']) ?></td>
                            <td>
                                <div class="cajaOperaciones">
                                    <button type="button" 
                                            class="btnOperacion btnModificar" 
                                            data-id="<?= htmlspecialchars($aula['ID']) ?>"
                                            data-tipo="<?= htmlspecialchars($aula['Tipo']) ?>"
                                            data-numero="<?= htmlspecialchars($aula['Numero']) ?>">
                                        Modificar
                                    </button>
                                    
                                    <form action="../app/controlador/procesarBajaAula.php" method="post" class="formularioEliminarAula" onsubmit="return confirm('¿Está seguro de eliminar esta aula?');">
                                        <input type="hidden" name="aulaId" value="<?= htmlspecialchars($aula['ID']) ?>">
                                        <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION["csrfToken"] ?? '') ?>">
                                        <button type="submit" class="btnOperacion btnEliminar">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </section>

        <dialog class="dialogAgregarAula" id="dialogAgregarAula">
            <button type="button" class="btnCerrarModal" id="btnCerrarModal">&times;</button>
            <form action="../app/controlador/procesarAgregarAula.php" method="post" id="formAgregarAula">
                <h2>Agregar Aula</h2>
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="laboratorio">Laboratorio</option>
                    <option value="taller">Taller</option>        
                </select>

                <label for="numero">Número:</label>
                <input type="text" name="numero" id="numero" pattern="[0-9]{2}" maxlength="2" inputmode="numeric" required>
                <input type="hidden" name="csrfToken" value="<?=htmlspecialchars($_SESSION["csrfToken"])?>">

                <button type="submit">Agregar</button>
                    
                
            </form>
        </dialog>
        <dialog class="dialogModificarAula" id="dialogModificarAula">
            <button type="button" class="btnCerrarModal" id="btnCerrarModalModificar">&times;</button>
            <form action="../app/controlador/procesarModificarAula.php" method="post" id="formModificarAula">
                <h2>Modificar Aula</h2>
                <input type="hidden" name="aulaId" id="modificarAulaId">

                <label for="modificarTipo">Tipo:</label>
                <select name="tipo" id="modificarTipo" required>
                    <option value="laboratorio">Laboratorio</option>
                    <option value="taller">Taller</option>        
                </select>

                <label for="modificarNumero">Número:</label>
                <input type="text" name="numero" id="modificarNumero" pattern="[0-9]{2}" maxlength="2" inputmode="numeric" required>
                <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($_SESSION["csrfToken"] ?? '') ?>">

                <button type="submit">Guardar Cambios</button>
            </form>
        </dialog>
    </main>
    <script src="assets/js/navbar_responsive.js"></script>
    <script src="assets/js/gestor_recursos.js"></script>
    
</body>
</html>
