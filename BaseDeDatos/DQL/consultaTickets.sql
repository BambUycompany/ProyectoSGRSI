SELECT t.PcNumPc, t.PcAulaID, a.Numero AS AulaNumero,
       CASE WHEN l.AulaID IS NOT NULL THEN 'laboratorio' ELSE 'taller' END AS AulaTipo,
       COUNT(*) AS CantidadReportes
FROM ticket t
INNER JOIN aula a ON a.ID = t.PcAulaID
LEFT JOIN laboratorio l ON l.AulaID = a.ID
GROUP BY t.PcNumPc, t.PcAulaID, a.Numero, l.AulaID
ORDER BY CantidadReportes DESC, a.Numero ASC, t.PcNumPc ASC;

SELECT t.ID, t.Descripcion, t.Fallo, t.Estado, t.Prioridad, t.Incidente, t.FechaCreacion,
       t.PcNumPc, t.PcAulaID, t.PlanillaId, a.Numero AS AulaNumero,
       CASE WHEN l.AulaID IS NOT NULL THEN 'laboratorio' ELSE 'taller' END AS AulaTipo
FROM ticket t
INNER JOIN aula a ON a.ID = t.PcAulaID
LEFT JOIN laboratorio l ON l.AulaID = a.ID
WHERE t.PcNumPc = :numPc AND t.PcAulaID = :aulaId
ORDER BY t.FechaCreacion DESC, t.ID DESC;

SELECT t.ID, t.Descripcion, t.Fallo, t.Estado, t.Prioridad, t.Incidente,
       t.FechaCreacion, t.PcNumPc, t.PcAulaID, t.PlanillaId
FROM ticket t
WHERE t.ID = :id;

SELECT t.ID, t.Descripcion, t.Fallo, t.Estado, t.Prioridad, t.Incidente,
       t.FechaCreacion, t.PcNumPc, t.PcAulaID, t.PlanillaId
FROM ticket t
WHERE t.PlanillaId = :planillaId
ORDER BY t.FechaCreacion ASC, t.ID ASC;