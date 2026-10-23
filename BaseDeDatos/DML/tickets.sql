INSERT INTO ticket
    (Descripcion, Fallo, Estado, FechaCreacion, PcNumPc, PcAulaID, SolicitanteCedula, PlanillaId)
VALUES
    (:descripcion, :fallo, 'pendiente', NOW(), :numPc, :aulaId, :cedula, :planillaId);

UPDATE ticket SET Estado = :estado WHERE ID = :id AND Estado <> 'finalizado';
UPDATE ticket SET Prioridad = :prioridad WHERE ID = :id AND Estado <> 'finalizado';

UPDATE ticket SET Estado = 'finalizado', Incidente = :diagnostico
WHERE ID = :id AND Estado <> 'finalizado';