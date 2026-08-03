/*
    Espacio donde se deberán colocar todas las insercciones utilizadas en la primer ejecución del programa para el testeo.
*/

INSERT INTO USUARIO (cedula, nombre, apellido, claveHash, activo) 
VALUES ('1111', 'Leandro', 'López', '$2y$10$VtWpUq.FkZZNsL5wYy3HWuQ318YPthJmrLGYHvI6T.HFEUBcodkdG', TRUE);
INSERT INTO ADMINISTRADOR (cedula) VALUES ('1111');

-- Usuario solicitante
INSERT INTO USUARIO (cedula, nombre, apellido, claveHash, activo) 
VALUES ('2222', 'Pepito', 'Alcachofas', '$2y$10$vvzq.erEeByM.9ac2pHarOlFKK8IZbL2VShQH382gF7k4f8QQxOeK', TRUE);
INSERT INTO SOLICITANTE (cedula) VALUES ('2222');

-- Usuario de soporte
INSERT INTO USUARIO (cedula, nombre, apellido, claveHash, activo) 
VALUES ('3333', 'María', 'González', '$2y$10$Emfmw6OWT2XsYUFSHg6rWOpJuHqVolvhoPwLcD1RAU90an6.uKrZG', TRUE);
INSERT INTO SOPORTE (cedula) VALUES ('3333');
/*
    "clave1234567" ~ "$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS"

    El hash se recuperó con el siguiente script para crear el primer usuario en el sistema
    Nota: En el futuro se deberían cargar por un usuario administrador

    <?php
        $return = password_hash('clave1234567', PASSWORD_DEFAULT);
        echo($return);
    ?>
*/