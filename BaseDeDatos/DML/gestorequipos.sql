INSERT INTO pc (NumPc, AulaID, Modelo, Monitor)
VALUES (:numPc, :aulaId, :modelo, :monitor);

INSERT INTO periferico (Tipo, Modelo, PcNumPc, PcAulaID)
VALUES (:tipo, :modelo, :pcNumPc, :pcAulaId);

UPDATE pc SET Modelo = :modelo, Monitor = :monitor
WHERE NumPc = :numPc AND AulaID = :aulaId;

UPDATE periferico SET Modelo = :modelo WHERE ID = :id;

INSERT INTO periferico (Tipo, Modelo, PcNumPc, PcAulaID)
VALUES (:tipo, :modelo, :numPc, :aulaId);

DELETE FROM periferico WHERE PcNumPc = :numPc AND PcAulaID = :aulaId;
DELETE FROM pc WHERE NumPc = :numPc AND AulaID = :aulaId;