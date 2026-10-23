INSERT INTO aula (Numero, CantDispositivos) VALUES (:numero, 0);
INSERT INTO laboratorio (AulaID) VALUES (:aulaId);
INSERT INTO taller (AulaID) VALUES (:aulaId);
UPDATE aula SET Numero = :numero WHERE ID = :aulaId;
DELETE FROM laboratorio WHERE AulaID = :aulaId;
DELETE FROM taller WHERE AulaID = :aulaId;
INSERT INTO laboratorio (AulaID) VALUES (:aulaId);
INSERT INTO taller (AulaID) VALUES (:aulaId);

DELETE FROM aula WHERE ID = :aulaId;