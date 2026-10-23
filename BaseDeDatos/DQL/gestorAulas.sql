SELECT aula.ID, aula.Numero,
       CASE WHEN EXISTS (SELECT 1 FROM laboratorio WHERE laboratorio.AulaID = aula.ID)
            THEN 'laboratorio' ELSE 'taller' END AS Tipo
FROM aula
WHERE aula.ID = :aulaId;

SELECT aula.ID AS ID, aula.Numero AS Numero,
       CASE WHEN EXISTS (SELECT 1 FROM laboratorio WHERE laboratorio.AulaID = aula.ID)
            THEN 'laboratorio' ELSE 'taller' END AS Tipo,
       (SELECT COUNT(*) FROM pc WHERE pc.AulaID = aula.ID) AS CantidadPcs
FROM aula
ORDER BY Tipo, aula.Numero;

SELECT 1
FROM aula a
INNER JOIN laboratorio t ON t.AulaID = a.ID
WHERE a.Numero = :numero AND a.ID <> :excluirId
LIMIT 1;

SELECT 1
FROM aula a
INNER JOIN taller t ON t.AulaID = a.ID
WHERE a.Numero = :numero AND a.ID <> :excluirId
LIMIT 1;

SELECT ID FROM aula WHERE ID = :aulaId FOR UPDATE;