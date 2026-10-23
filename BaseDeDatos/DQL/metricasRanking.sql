
SELECT t.PcNumPc, t.PcAulaID, a.Numero AS AulaNumero,
       CASE WHEN l.AulaID IS NOT NULL THEN 'laboratorio' ELSE 'taller' END AS AulaTipo,
       COUNT(*) AS CantidadReportes
FROM ticket t
INNER JOIN aula a ON a.ID = t.PcAulaID
LEFT JOIN laboratorio l ON l.AulaID = a.ID
WHERE 1 = 1

GROUP BY t.PcNumPc, t.PcAulaID, a.Numero, l.AulaID
ORDER BY CantidadReportes DESC, a.Numero, t.PcNumPc
LIMIT 5;

SELECT a.ID, a.Numero,
       CASE WHEN l.AulaID IS NOT NULL THEN 'laboratorio' ELSE 'taller' END AS Tipo,
       COUNT(t.ID) AS CantidadTickets
FROM aula a
INNER JOIN ticket t ON t.PcAulaID = a.ID
LEFT JOIN laboratorio l ON l.AulaID = a.ID
WHERE 1 = 1

GROUP BY a.ID, a.Numero, l.AulaID
ORDER BY CantidadTickets DESC, a.Numero
LIMIT 5;

SELECT Fallo, COUNT(*) AS CantidadReportes
FROM ticket
WHERE 1 = 1

GROUP BY Fallo
ORDER BY CantidadReportes DESC, Fallo ASC;