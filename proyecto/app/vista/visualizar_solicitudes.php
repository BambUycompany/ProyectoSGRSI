<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Solicitudes</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/registroSalaCSS.css">
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
        <section class="seccionVisualizarRegistros">
            <h2>Visualizar Registros de uso de laboratorios</h2>
            <div class="filtrarFecha">
                <button type="button">Filtrar por fecha de expedición</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Tipo de aula</th>
                        <th>Número de PC</th>
                        <th>Número de laboratorio</th>
                        <th>Problema</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>Mario López</td>
                        <td>PC-04</td>
                        <td>Lab 2</td>
                        <td>No enciende</td>
                        <td>En proceso</td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Ana Pérez</td>
                        <td>PC-11</td>
                        <td>Lab 1</td>
                        <td>Teclado malo</td>
                        <td>Por hacer</td>
                    </tr>
                    <tr>
                        <td>003</td>
                        <td>Carlos Gómez</td>
                        <td>PC-07</td>
                        <td>Lab 3</td>
                        <td>Red no conecta</td>
                        <td>En proceso</td>
                    </tr>
                    <tr>
                        <td>004</td>
                        <td>María Ruiz</td>
                        <td>PC-02</td>
                        <td>Lab 2</td>
                        <td>Monitor parpadea</td>
                        <td>Culminado</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
    <script src="../js/navbar_responsive.js"></script>

    
</body>
</html>