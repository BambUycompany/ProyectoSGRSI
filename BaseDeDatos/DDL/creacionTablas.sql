

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


CREATE TABLE habilita (
    AdministradorCedula VARCHAR(20) NOT NULL,
    AulaID                  INT NOT NULL,
    FechaHabilitacion       DATE NOT NULL,
    PRIMARY KEY (AdministradorCedula, AulaID),
    FOREIGN KEY (AdministradorCedula) REFERENCES administrador(cedula) ON DELETE CASCADE,
    FOREIGN KEY (AulaID) REFERENCES aula(ID) ON DELETE CASCADE
);


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
    PcNumPc VARCHAR(20) NOT NULL,         
    PcAulaID INT NOT NULL,
    FOREIGN KEY (PcNumPc, PcAulaID) REFERENCES PC(NumPc, AulaID) ON DELETE CASCADE
);

CREATE TABLE PORTATIL (
    ID     INT AUTO_INCREMENT PRIMARY KEY,
    Modelo VARCHAR(100) NOT NULL,
    Estado VARCHAR(30) NOT NULL
);


CREATE TABLE PLANILLA (
    ID                     INT AUTO_INCREMENT PRIMARY KEY,
    Fecha                  DATE NOT NULL,
    HoraEntrada            TIME NOT NULL,
    HoraSalida             TIME,
    Grupo                  VARCHAR(50),
    Turno                  VARCHAR(30),
    Asignatura             VARCHAR(100),
    NombreSolicitante      VARCHAR(150),               
    CedulaRegistrante   VARCHAR(20) NOT NULL,         
    AulaID                 INT NOT NULL,                 
    FOREIGN KEY (CedulaRegistrante) REFERENCES USUARIO(Cedula),
    FOREIGN KEY (AulaID) REFERENCES aula(ID)
);

CREATE TABLE TICKET (
    ID                    INT AUTO_INCREMENT PRIMARY KEY,
    Descripcion           TEXT,
    Incidente             VARCHAR(150),
    Fallo                 VARCHAR(150),
    Estado                VARCHAR(30) NOT NULL,          
    Prioridad             VARCHAR(20) NOT NULL DEFAULT '0',          
    PcNumPc               VARCHAR(20) NOT NULL,         
    PcAulaID              INT NOT NULL,
    SolicitanteCedula     VARCHAR(20) NOT NULL,         
    SoporteCedula         VARCHAR(20) NOT NULL,          
    PlanillaId            INT UNIQUE,                   
    FOREIGN KEY (PcNumPc, PcAulaID) REFERENCES PC(NumPc, AulaID) ON DELETE CASCADE,
    FOREIGN KEY (SolicitanteCedula) REFERENCES SOLICITANTE(Cedula),
    FOREIGN KEY (SoporteCedula) REFERENCES SOPORTE(Cedula),
    FOREIGN KEY (PlanillaId) REFERENCES PLANILLA(Id)
);


CREATE TABLE PRESTAMO (
    ID                     INT AUTO_INCREMENT PRIMARY KEY,
    FechaDev               DATE NOT NULL,
    AlumnoClase             VARCHAR(50) NOT NULL,  
    AlumnoTelefono          VARCHAR(20) NOT NULL,   
    AlumnoCorreo            VARCHAR(100) NOT NULL, 
    AlumnoNombre            VARCHAR(100) NOT NULL,   
    AlumnoCedula   VARCHAR(20) NOT NULL,   
    PortatilID             INT NOT NULL UNIQUE,    
   
    FOREIGN KEY (PortatilID) REFERENCES PORTATIL(ID)
);