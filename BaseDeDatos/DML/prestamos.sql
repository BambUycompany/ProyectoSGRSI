INSERT INTO prestamo
    (FechaPrestamo, FechaDev, Estado, CIAlumno, Clase, CorreoAlumno, TelefonoAlumno, SolicitanteCedula, PortatilID)
VALUES
    (CURDATE(), CURDATE(), 'activo', :ciAlumno, :clase, :correoAlumno, :telefonoAlumno, :cedula, :portatilId);

UPDATE portatil SET Estado = 'en_prestamo'
WHERE ID = :portatilId AND Estado = 'disponible';

UPDATE prestamo SET Estado = 'finalizado' WHERE ID = :id AND Estado = 'activo';

UPDATE portatil SET Estado = 'disponible'
WHERE ID = :portatilId AND Estado = 'en_prestamo';

UPDATE prestamo
SET CIAlumno = :ci, Clase = :clase, CorreoAlumno = :correo, TelefonoAlumno = :telefono
WHERE ID = :id;

DELETE FROM prestamo WHERE ID = :id AND Estado = 'finalizado';