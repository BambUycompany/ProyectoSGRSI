/*
    Espacio donde se deberán aclarar y definir todas las consultas utilizadas dentro del sistema

    Ya que los select tendrán valores que dependerán de lo que ingrese el usuario, indicar siempre cuál es el valor
    que variará.
*/

SELECT u.cedula, u.nombre, u.apellido, u.claveHash, u.activo,
       CASE WHEN a.cedula  IS NOT NULL THEN 1 ELSE 0 END AS administrador,
       CASE WHEN so.cedula IS NOT NULL THEN 1 ELSE 0 END AS soporte,
       CASE WHEN s.cedula  IS NOT NULL THEN 1 ELSE 0 END AS solicitante
FROM usuario u
LEFT JOIN administrador a ON a.cedula  = u.cedula
LEFT JOIN soporte      so ON so.cedula = u.cedula
LEFT JOIN solicitante   s ON s.cedula  = u.cedula
WHERE u.cedula = :cedula;

SELECT u.cedula, u.nombre, u.apellido, u.activo,
       CASE WHEN a.cedula  IS NOT NULL THEN 1 ELSE 0 END AS administrador,
       CASE WHEN so.cedula IS NOT NULL THEN 1 ELSE 0 END AS soporte,
       CASE WHEN s.cedula  IS NOT NULL THEN 1 ELSE 0 END AS solicitante
FROM usuario u
LEFT JOIN administrador a ON a.cedula  = u.cedula
LEFT JOIN soporte      so ON so.cedula = u.cedula
LEFT JOIN solicitante   s ON s.cedula  = u.cedula
ORDER BY u.nombre, u.apellido;

SELECT u.cedula, u.nombre, u.apellido, u.activo,
       CASE WHEN a.cedula  IS NOT NULL THEN 1 ELSE 0 END AS administrador,
       CASE WHEN so.cedula IS NOT NULL THEN 1 ELSE 0 END AS soporte,
       CASE WHEN s.cedula  IS NOT NULL THEN 1 ELSE 0 END AS solicitante
FROM usuario u
LEFT JOIN administrador a ON a.cedula  = u.cedula
LEFT JOIN soporte      so ON so.cedula = u.cedula
LEFT JOIN solicitante   s ON s.cedula  = u.cedula
WHERE u.cedula = :cedula;