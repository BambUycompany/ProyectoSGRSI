

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
    FOREIGN KEY (AulaID) REFERENCES aula(ID) ON DELETE CASCADE);

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
    CedulaRegistrante   VARCHAR(20) NOT NULL,         
    AulaID                 INT NOT NULL,                 
    FOREIGN KEY (CedulaRegistrante) REFERENCES USUARIO(Cedula),
    FOREIGN KEY (AulaID) REFERENCES aula(ID)
);


CREATE TABLE ticket (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Descripcion TEXT NOT NULL,
    Incidente VARCHAR(150) NULL,
    Fallo VARCHAR(150) NOT NULL,
    Estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    Prioridad VARCHAR(20) NULL,
    PcNumPc VARCHAR(20) NOT NULL,
    PcAulaID INT NOT NULL,
    SolicitanteCedula VARCHAR(20) NOT NULL,
    SoporteCedula VARCHAR(20) NULL,
    PlanillaId INT NOT NULL,
    FechaCreacion DATETIME NOT NULL,
    FOREIGN KEY (PcNumPc, PcAulaID)
        REFERENCES pc(NumPc, AulaID),
    FOREIGN KEY (SolicitanteCedula)
        REFERENCES solicitante(cedula),
    FOREIGN KEY (SoporteCedula)
        REFERENCES soporte(cedula),
    INDEX (PlanillaId)
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