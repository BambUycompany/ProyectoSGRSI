CREATE TABLE USUARIO (
    cedula CHAR(8) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    claveHash VARCHAR(255) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT FALSE,

    CONSTRAINT pk_usuario
        PRIMARY KEY (cedula)
);

CREATE TABLE ADMINISTRADOR (
    cedula CHAR(8) NOT NULL,

    CONSTRAINT pk_administrador
        PRIMARY KEY (cedula)
);

CREATE TABLE SOLICITANTE (
    cedula CHAR(8) NOT NULL,

    CONSTRAINT pk_solicitante
        PRIMARY KEY (cedula)
);

CREATE TABLE SOPORTE (
    cedula CHAR(8) NOT NULL,

    CONSTRAINT pk_soporte
        PRIMARY KEY (cedula)
);

CREATE TABLE ROL ( 
    cedula CHAR(8) NOT NULL,
    rol VARCHAR(50) NOT NULL,

    CONSTRAINT pk_rol
        PRIMARY KEY (cedula, rol)
);


ALTER TABLE ADMINISTRADOR
    ADD CONSTRAINT fk_administrador_usuario
    FOREIGN KEY (cedula)
    REFERENCES USUARIO (cedula);

ALTER TABLE SOLICITANTE
    ADD CONSTRAINT fk_solicitante_usuario
    FOREIGN KEY (cedula)
    REFERENCES USUARIO (cedula);

ALTER TABLE SOPORTE
    ADD CONSTRAINT fk_soporte_usuario
    FOREIGN KEY (cedula)
    REFERENCES USUARIO (cedula);

ALTER TABLE ROL
    ADD CONSTRAINT fk_rol_usuario
    FOREIGN KEY (cedula)
    REFERENCES USUARIO (cedula);