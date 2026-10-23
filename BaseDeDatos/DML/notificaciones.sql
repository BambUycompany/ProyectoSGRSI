INSERT INTO notificacion (TicketID, DestinatarioCedula, Leida, FechaCreacion)
SELECT t.ID, t.SolicitanteCedula, 0, NOW()
FROM ticket t
WHERE t.ID = :ticketId
  AND t.Estado = 'finalizado'
  AND t.SolicitanteCedula IS NOT NULL
  AND TRIM(t.SolicitanteCedula) <> '';

UPDATE notificacion
SET Leida = 1
WHERE ID = :id
  AND DestinatarioCedula = :cedula
  AND TicketID IN (
      SELECT t.ID FROM ticket t
      WHERE t.SolicitanteCedula = :cedulaTicket AND t.Estado = 'finalizado'
  );