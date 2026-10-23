SELECT ID, Descripcion, Fallo, Estado, PcNumPc, FechaCreacion
FROM ticket
WHERE PlanillaId = :planillaId
ORDER BY FechaCreacion;

SELECT aula.Numero, 'laboratorio' AS Tipo
FROM aula JOIN laboratorio ON aula.ID = laboratorio.AulaID
UNION ALL
SELECT aula.Numero, 'taller' AS Tipo
FROM aula JOIN taller ON aula.ID = taller.AulaID;

SELECT aula.ID FROM aula
JOIN laboratorio ON aula.ID = laboratorio.AulaID
WHERE aula.Numero = :numero;
SELECT aula.ID FROM aula
JOIN taller ON aula.ID = taller.AulaID
WHERE aula.Numero = :numero;

SELECT p.ID, p.Fecha, p.HoraEntrada, p.HoraSalida, p.Grupo, p.Turno, p.Asignatura,
       p.NombreSolicitante, p.CedulaRegistrante, a.Numero AS AulaNumero,
       CASE WHEN l.AulaID IS NOT NULL THEN 'laboratorio' ELSE 'taller' END AS AulaTipo
FROM planilla p
INNER JOIN aula a ON a.ID = p.AulaID
LEFT JOIN laboratorio l ON l.AulaID = a.ID
WHERE 1 = 1

ORDER BY p.Fecha DESC, p.HoraEntrada DESC, p.ID DESC;


SELECT p.ID, p.Fecha, p.HoraEntrada, p.HoraSalida, p.Grupo, p.Turno, p.Asignatura,
       p.NombreSolicitante, p.CedulaRegistrante, p.AulaID, a.Numero AS AulaNumero,
       CASE WHEN l.AulaID IS NOT NULL THEN 'laboratorio' ELSE 'taller' END AS AulaTipo
FROM planilla p
INNER JOIN aula a ON a.ID = p.AulaID
LEFT JOIN laboratorio l ON l.AulaID = a.ID
WHERE p.ID = :planillaId;


SELECT 1 FROM pc WHERE NumPc = :numPc AND AulaID = :aulaId LIMIT 1;