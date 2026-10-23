INSERT INTO portatil (Modelo, Estado) VALUES (:modelo, 'disponible');

UPDATE portatil SET Modelo = :modelo WHERE ID = :id;

UPDATE portatil SET Estado = 'deshabilitado' WHERE ID = :id AND Estado = 'disponible';

UPDATE portatil SET Estado = 'disponible' WHERE ID = :id AND Estado = 'deshabilitado';

DELETE FROM portatil WHERE ID = :id AND Estado = 'disponible';