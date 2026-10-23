SELECT COUNT(*) FROM ticket WHERE 1 = 1
;

SELECT COUNT(*) FROM planilla WHERE 1 = 1
;

SELECT COUNT(*) FROM ticket WHERE Estado = :estado
;

SELECT COUNT(*) FROM ticket WHERE Fallo = 'bajo_rendimiento'
;

SELECT COUNT(*) FROM portatil WHERE Estado = 'en_prestamo';