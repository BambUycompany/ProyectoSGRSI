SELECT t.PcNumPc, t.PcAulaID, a.Numero AS AulaNumero,
       CASE WHEN l.AulaID IS NOT NULL THEN 'laboratorio' ELSE 'taller' END AS AulaTipo,
       COUNT(*) AS CantidadVandalismo
FROM ticket t
INNER JOIN aula a ON a.ID = t.PcAulaID
LEFT JOIN laboratorio l ON l.AulaID = a.ID
WHERE t.Fallo IN ('falta_mouse', 'falta_teclado')

GROUP BY t.PcNumPc, t.PcAulaID, a.Numero, l.AulaID
ORDER BY CantidadVandalismo DESC
LIMIT 1;

SELECT t.PcNumPc, t.PcAulaID, a.Numero AS AulaNumero,
       CASE WHEN l.AulaID IS NOT NULL THEN 'laboratorio' ELSE 'taller' END AS AulaTipo,
       COUNT(*) AS CantidadVandalismo
FROM ticket t
INNER JOIN aula a ON a.ID = t.PcAulaID
LEFT JOIN laboratorio l ON l.AulaID = a.ID
WHERE t.Fallo IN ('falta_mouse', 'falta_teclado')

GROUP BY t.PcNumPc, t.PcAulaID, a.Numero, l.AulaID
HAVING COUNT(*) >= :umbral
ORDER BY CantidadVandalismo DESC;

SELECT p.Modelo, COUNT(*) AS CantidadReportes
FROM ticket t
INNER JOIN periferico p
        ON p.PcNumPc = t.PcNumPc AND p.PcAulaID = t.PcAulaID AND p.Tipo = 'mouse'
WHERE t.Fallo IN ('falta_mouse', 'mouse_no_funciona')

GROUP BY p.Modelo
ORDER BY CantidadReportes DESC
LIMIT 1;

SELECT p.Modelo, COUNT(*) AS CantidadReportes
FROM ticket t
INNER JOIN periferico p
        ON p.PcNumPc = t.PcNumPc AND p.PcAulaID = t.PcAulaID AND p.Tipo = 'teclado'
WHERE t.Fallo IN ('falta_teclado', 'teclado_no_funciona')

GROUP BY p.Modelo
ORDER BY CantidadReportes DESC
LIMIT 1;