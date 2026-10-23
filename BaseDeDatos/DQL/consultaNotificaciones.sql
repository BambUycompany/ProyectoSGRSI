SELECT n.ID, n.TicketID, n.Leida, n.FechaCreacion, t.PcNumPc, t.PcAulaID, t.PlanillaId,
       t.Incidente AS Diagnostico, a.Numero AS AulaNumero,
       CASE WHEN l.AulaID IS NOT NULL THEN 'laboratorio' ELSE 'taller' END AS AulaTipo
FROM notificacion n
INNER JOIN ticket t ON t.ID = n.TicketID
INNER JOIN aula a ON a.ID = t.PcAulaID
LEFT JOIN laboratorio l ON l.AulaID = a.ID
WHERE n.DestinatarioCedula = :cedula
  AND t.SolicitanteCedula = :cedulaTicket
  AND t.Estado = 'finalizado'
ORDER BY n.FechaCreacion DESC, n.ID DESC
LIMIT 50;

SELECT COUNT(*)
FROM notificacion n
INNER JOIN ticket t ON t.ID = n.TicketID
WHERE n.DestinatarioCedula = :cedula
  AND t.SolicitanteCedula = :cedulaTicket
  AND t.Estado = 'finalizado'
  AND n.Leida = 0;

SELECT COUNT(*)
FROM notificacion n
INNER JOIN ticket t ON t.ID = n.TicketID
WHERE n.ID = :id
  AND n.DestinatarioCedula = :cedula
  AND t.Sos 1;