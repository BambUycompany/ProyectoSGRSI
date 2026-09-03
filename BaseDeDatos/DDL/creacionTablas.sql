
-- Registro de aula

CREATE TABLE aula (
    ID               INT AUTO_INCREMENT PRIMARY KEY,
    Numero           VARCHAR(20) NOT NULL,
    CantDispositivos INT NOT NULL DEFAULT 0
);

CREATE TABLE laboratorio (
    AulaID INT PRIMARY KEY,
    FOREIGN KEY (AulaID) REFERENCES aula(ID) ON DELETE CASCADE 
);

CREATE TABLE taller (
    AulaID INT PRIMARY KEY,
    FOREIGN KEY (AulaID) REFERENCES aula(ID) ON DELETE CASCADE
);

-- ===== HABILITA (N:M entre ADMINISTRATIVO y AULA) =====

CREATE TABLE habilita (
    AdministradorCedula VARCHAR(20) NOT NULL,
    AulaID                  INT NOT NULL,
    FechaHabilitacion       DATE NOT NULL,
    PRIMARY KEY (AdministradorCedula, AulaID),
    FOREIGN KEY (AdministradorCedula) REFERENCES administrador(cedula) ON DELETE CASCADE,
    FOREIGN KEY (AulaID) REFERENCES aula(ID) ON DELETE CASCADE
);

-- ===== EQUIPAMIENTO =====

CREATE TABLE PC (
    NumPc   VARCHAR(20),
    AulaID  INT NOT NULL,
    PRIMARY KEY (NumPc,AulaID),
    Modelo  VARCHAR(100) NOT NULL,
    AulaID REFERENCES aula(ID) ON DELETE CASCADE
);

CREATE TABLE PERIFERICO (
    ID      INT AUTO_INCREMENT PRIMARY KEY,
    Modelo  VARCHAR(100) NOT NULL,
    PcNumPc VARCHAR(20) NOT NULL,          -- Conectado: PC (1) -- (N) PERIFERICO
    PcAulaID INT NOT NULL,
    FOREIGN KEY (PcNumPc, PcAulaID) REFERENCES PC(NumPc, AulaID) ON DELETE CASCADE
);

CREATE TABLE PORTATIL (
    ID     INT AUTO_INCREMENT PRIMARY KEY,
    Modelo VARCHAR(100) NOT NULL,
    Estado VARCHAR(30) NOT NULL
);

-- ===== PLANILLA =====

CREATE TABLE PLANILLA (
    ID                     INT AUTO_INCREMENT PRIMARY KEY,
    Fecha                  DATE NOT NULL,
    HoraEntrada            TIME NOT NULL,
    HoraSalida             TIME,
    Grupo                  VARCHAR(50),
    Turno                  VARCHAR(30),
    Asignatura             VARCHAR(100),
    NombreSolicitante      VARCHAR(150),               -- dato descriptivo, tal como aparece en el DER
    CedulaRegistrante   VARCHAR(20) NOT NULL,         -- Registra: puede ser un SOLICITANTE o un SOPORTE
    AulaID                 INT NOT NULL,                 -- Referida/Incluye: unificadas en una sola FK
    FOREIGN KEY (CedulaRegistrante) REFERENCES USUARIO(Cedula),
    FOREIGN KEY (AulaID) REFERENCES aula(ID)
);

-- ===== TICKET =====

CREATE TABLE TICKET (
    ID                    INT AUTO_INCREMENT PRIMARY KEY,
    Descripcion           TEXT,
    Incidente             VARCHAR(150),
    Fallo                 VARCHAR(150),
    Estado                VARCHAR(30) NOT NULL,          -- atributo de "Recepciona"
    Prioridad             VARCHAR(20) NOT NULL DEFAULT '0',          -- atributo de "Recepciona"
    PcNumPc               VARCHAR(20) NOT NULL,          -- Tiene: PC (1) -- (N) TICKET
    PcAulaID              INT NOT NULL,
    SolicitanteCedula     VARCHAR(20) NOT NULL,          -- Genera: SOLICITANTE (1) -- (N) TICKET
    SoporteCedula         VARCHAR(20) NOT NULL,          -- Recepciona: SOPORTE (1) -- (N) TICKET
    PlanillaId            INT UNIQUE,                    -- Reportado: 0..1 (nullable, único)
    FOREIGN KEY (PcNumPc, PcAulaID) REFERENCES PC(NumPc, AulaID) ON DELETE CASCADE,
    FOREIGN KEY (SolicitanteCedula) REFERENCES SOLICITANTE(Cedula),
    FOREIGN KEY (SoporteCedula) REFERENCES SOPORTE(Cedula),
    FOREIGN KEY (PlanillaId) REFERENCES PLANILLA(Id)
);

-- ===== PRESTAMO =====

CREATE TABLE PRESTAMO (
    ID                     INT AUTO_INCREMENT PRIMARY KEY,
    FechaDev               DATE NOT NULL,
    AlumnoClase             VARCHAR(50) NOT NULL,   -- Solicita: SOLICITANTE (1) -- (N) PRESTAMO
    AlumnoTelefono          VARCHAR(20) NOT NULL,   -- Solicita: SOLICITANTE (1) -- (N) PRESTAMO
    AlumnoCorreo            VARCHAR(100) NOT NULL,  -- Solicita: SOLIC
    AlumnoNombre            VARCHAR(100) NOT NULL,   -- Solicita: SOLICITANTE (1) -- (N) PRESTAMO
    AlumnoCedula   VARCHAR(20) NOT NULL,   -- Solicita: SOLICITANTE (1) -- (N) PRESTAMO
    PortatilID             INT NOT NULL UNIQUE,     -- Asigna: 1:1 con PORTATIL
   
    FOREIGN KEY (PortatilID) REFERENCES PORTATIL(ID)
);