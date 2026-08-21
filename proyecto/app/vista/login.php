
<!DOCTYPE html>
<html lang="en,es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../public/assets/css/loginCSS.css">
    <link rel="stylesheet" href="../public/assets/css/global.css">
    <title>Ingreso</title>
</head>
<body>
    <main>
        
    <?php
    $mensajesError = [
        "credenciales"     => "La cédula o la contraseña son incorrectas. Intente nuevamente.",
        "usuarioInactivo"  => "El usuario se encuentra inactivo. Contactese con el administrador del sistema.",
        "sinRol"           => "Este usuario no tiene ningún rol asignado. Contactese con el administrador del sistema.",
        "sinSesion"        => "No esta habilitado para ingresar a esta pagina. Inicie sesion nuevamente.",
        "noAutorizado"     => "No esta autorizado para ingresar a esta pagina. Inicie sesion nuevamente."
    ];

    if (isset($_GET['error']) && isset($mensajesError[$_GET['error']])) { 
        $mensaje = $mensajesError[$_GET['error']];
        echo "<div class='mensajeError'>$mensaje</div>";
    }
    ?>
        
        <section class="seccionLogin">
            <h2>Ingreso al sistema</h2>
            <form action="../controlador/procesarLogin.php" method="POST">
               
                <div class="cajaDatos">
                    <label for="cedula">Cedula:</label>
                    <input type="text" id="cedula" name="cedula" pattern="[0-9]{8}" maxlength="8" inputmode="numeric" required><br><br>
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
