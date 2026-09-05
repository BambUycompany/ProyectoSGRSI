<!DOCTYPE html>
<html lang="es, en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de aulas</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/registroAulaCSS.css">

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

             <<h1><a href="administrador.php"><img src="../public/assets/img/imagen_2026-05-28_201450907-removebg-preview.png" alt="Logo " class="logo"> S.G.R.S.I </a></h1>
            <ul class="listaNavegacion">
               
                <li data-roles="solicitante administrador soporte"><a href="registro_sala.php" class="botones">Registro Sala</a></li>
                <li data-roles="administrador soporte"><a href="metricas.php" class="botones">Métricas</a></li>
                <li data-roles="soporte"><a href="listado_tickets.php" class="botones">Tickets</a></li>
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
        <section class="seccionRegistroAulas">
             <div class="cabeceraTabla">
                <h2>Registro de aulas</h2>
                <button type="button" class="btnOperacion" id="btnAgregarAula">Agregar Aula</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Numero</th>
                        <th>Cantidad</th>
                        <th id="colOperaciones">Operaciones</th>
                    </tr>
                </thead>
                <tbody id="listadoTablaAulas">
                </tbody>
            </table>
        </section>

        <dialog class="dialogAgregarAula" >
            <button type="button" class="btnCerrarModal" id="btnCerrarAgregarAula">x</button>
            <form action="registro_aulas.php" method="post" id="formAgregarAula">
                <label for="tipo">Tipo de aula:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="Taller">Taller</option>
                    <option value="Laboratorio">Laboratorio</option>
                </select>

                <label for="numero">Numero:</label>
                <select type="text" id="numero" name="numero" pattern="[0-9]+" required>
                    <option value="">Ingrese un numero</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                </select>

                <label for="cantidad">Cantidad de dispositivos:</label>
                <select id="cantidad" name="cantidad" required>
                    <option value="">Seleccione una cantidad</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                </select>
                <button type="submit">Agregar</button>
            </form>
        </dialog>
            
        </section>
    </main>
    <script src="../public/assets/js/navbar_responsive.js"></script>
    <script src="../public/assets/js/registro_aulas.js"></script>
    

</body>
</html>