SELECT NumPc, Modelo, Monitor FROM pc WHERE AulaID = :aulaId ORDER BY NumPc;
SELECT Tipo, Modelo, PcNumPc FROM periferico WHERE PcAulaID = :aulaId;

SELECT NumPc FROM pc WHERE AulaID = :aulaId;

SELECT 1 FROM pc WHERE NumPc = :numPc AND AulaID = :aulaId FOR UPDATE;

SELECT ID FROM periferico
WHERE PcNumPc = :numPc AND PcAulaID = :aulaId AND Tipo = :tipo
FOR UPDATE;