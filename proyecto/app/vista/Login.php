<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/css/loginCSS.css">
    <link rel="stylesheet" href="assets/css/global.css">
    <title>Ingreso</title>
</head>
<body>
    <main>
        <section class="seccionLogin">
            <h2>Ingreso al sistema</h2>
            <form action="../app/controlador/procesarLogin.php" method="post">
               
                <div class="cajaDatos">
                    <label for="cedula">Cedula:</label>
                    <input type="text" id="cedula" name="cedula" required><br><br>
                </div>
                <div class="cajaDatos">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required><br><br>
                </div>
                <input type="submit" value="Ingresar" class="botones">
            </form>
            <span>¿No tienes una cuenta? <a href="mailto:direccioniti.cetp">Solicitala aqui </a> atraves del mail: direccioniti.cetp@gmail.com</span>
        </section>
        
    </main>
</body>
</html>