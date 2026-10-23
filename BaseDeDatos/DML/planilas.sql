INSERT INTO planilla
    (Fecha, HoraEntrada, HoraSalida, Grupo, Turno, Asignatura, NombreSolicitante, CedulaRegistrante, AulaID)
VALUES
    (:fecha, :horaEntrada, :horaSalida, :grupo, :turno, :asignatura, :nombreSolicitante, :cedulaRegistrante, :aulaId);

INSERT INTO ticket
    (Descripcion, Fallo, Estado, FechaCreacion, PcNumPc, PcAulaID, SolicitanteCedula, PlanillaId)
VALUES
    (:descripcion, :fallo, 'pendiente', NOW(), :pcNumPc, :pcAulaID, :solicitanteCedula, :planillaId);