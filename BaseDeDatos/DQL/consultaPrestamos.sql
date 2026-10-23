SELECT ID, Modelo FROM portatil WHERE Estado = 'disponible' ORDER BY Modelo, ID;

SELECT Estado FROM portatil WHERE ID = :portatilId FOR UPDATE;

SELECT p.ID, p.FechaPrestamo, p.FechaDev, p.Estado, p.CIAlumno, p.Clase,
       p.CorreoAlumno, p.TelefonoAlumno, p.SolicitanteCedula, p.PortatilID,
       pt.Modelo AS PortatilModelo
FROM prestamo p
INNER JOIN portatil pt ON pt.ID = p.PortatilID
WHERE p.SolicitanteCedula = :cedula
ORDER BY CASE WHEN p.Estado = 'activo' THEN 0 ELSE 1 END, p.FechaPrestamo DESC, p.ID DESC;

SELECT p.ID, p.FechaPrestamo, p.FechaDev, p.Estado, p.CIAlumno, p.Clase,
       p.CorreoAlumno, p.TelefonoAlumno, p.SolicitanteCedula, p.PortatilID,
       pt.Modelo AS PortatilModelo, u.Nombre AS DocenteNombre, u.Apellido AS DocenteApellido
FROM prestamo p
INNER JOIN portatil pt ON pt.ID = p.PortatilID
INNER JOIN usuario u ON u.cedula = p.SolicitanteCedula
ORDER BY CASE WHEN p.Estado = 'activo' THEN 0 ELSE 1 END, p.FechaPrestamo DESC, p.ID DESC;

SELECT * FROM prestamo WHERE ID = :id;

SELECT PortatilID, Estado FROM prestamo WHERE ID = :id FOR UPDATE;