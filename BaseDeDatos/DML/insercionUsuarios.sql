/*
    Espacio donde se deberán colocar todas las insercciones utilizadas en la primer ejecución del programa para el testeo.
*/
//Usuario administrador
INSERT INTO USUARIO (cedula, nombre, apellido, claveHash, activo) 
VALUES ('1111', 'Leandro', 'López', '$2y$10$VtWpUq.FkZZNsL5wYy3HWuQ318YPthJmrLGYHvI6T.HFEUBcodkdG', TRUE);
INSERT INTO ADMINISTRADOR (cedula) VALUES ('1111');

//Usuario solicitante
INSERT INTO USUARIO (cedula, nombre, apellido, claveHash, activo) 
VALUES ('2222', 'Pepito', 'Alcachofas', '$2y$10$vvzq.erEeByM.9ac2pHarOlFKK8IZbL2VShQH382gF7k4f8QQxOeK', TRUE);
INSERT INTO SOLICITANTE (cedula) VALUES ('2222');

// Usuario de soporte
INSERT INTO USUARIO (cedula, nombre, apellido, claveHash, activo) 
VALUES ('3333', 'María', 'González', '$2y$10$Emfmw6OWT2XsYUFSHg6rWOpJuHqVolvhoPwLcD1RAU90an6.uKrZG', TRUE);
INSERT INTO SOPORTE (cedula) VALUES ('3333');

//Usuario con dos rols
INSERT INTO usuario (cedula, nombre, apellido, claveHash, activo)
VALUES ('87654321', 'Elvis', 'Fernandez', '$2y$10$p6h7E6FLv.gx6N9GG6lh3ecLVdNnLH5gGZxj0AmlibC6lTiHXMAaS', TRUE);
INSERT INTO soporte (cedula) VALUES ('87654321');
INSERT INTO solicitante (cedula) VALUES ('87654321');

//Usuario con tres roles
INSERT INTO usuario (cedula, nombre, apellido, claveHash, activo)
VALUES ('87654322', 'Maria', 'Gonzalez', '$2y$10$UQ.xRRJNSAY8LszoLR9tiunHONTWLuu.RbLK2LjiQY93esXYtrAiK', TRUE);

INSERT INTO administrador (cedula) VALUES ('87654322');
INSERT INTO soporte (cedula) VALUES ('87654322');
INSERT INTO solicitante (cedula) VALUES ('87654322');

//Usuario sin rol
INSERT INTO usuario (cedula, nombre, apellido, claveHash, activo)
VALUES ('87654323', 'Juan', 'Perez', '$2y$10$V5k2en.l4goJX8kkUzGQVeJR.qK3/W0bFT.56xK02OffnZ01rLcw6', TRUE);

/*
    "clave1234567" ~ "$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS"

    El hash se recuperó con el siguiente script para crear el primer usuario en el sistema
    Nota: En el futuro se deberían cargar por un usuario administrador

    <?php
        $return = password_hash('clave1234567', PASSWORD_DEFAULT);
        echo($return);
    ?>
*/