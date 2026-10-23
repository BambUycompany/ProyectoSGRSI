SELECT ID, Modelo, Estado FROM portatil ORDER BY Modelo;

SELECT ID, Modelo FROM portatil WHERE Estado = 'disponible' ORDER BY Modelo;

SELECT ID, Modelo, Estado FROM portatil WHERE ID = :id;