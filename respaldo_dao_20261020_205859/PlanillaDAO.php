<?php

class PlanillaDAO {
    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     *
     * @param PDO $conexion La conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
     */

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

/**
     * Registra una nueva planilla en la base de datos.
     *
     * @param array $datos Array asociativo con las claves: fecha, horaEntrada, horaSalida,
     * grupo, turno, asignatura, nombreSolicitante, documentoRegistrante y aulaId.
     *
     * @return string El ID autogenerado de la planilla recién insertada.
     */

    public function registrarPlanilla(array $datos) {
        $sql = "INSERT INTO PLANILLA 
            (Fecha, HoraEntrada, HoraSalida, Grupo, Turno, Asignatura, nombreSolicitante, cedulaRegistrante, AulaID)
            VALUES (:fecha, :horaEntrada, :horaSalida, :grupo, :turno, :asignatura, :nombreSolicitante, :cedulaRegistrante, :aulaId)";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "fecha" => $datos["fecha"],
            "horaEntrada" => $datos["horaEntrada"],
            "horaSalida" => $datos["horaSalida"],
            "grupo" => $datos["grupo"],
            "turno" => $datos["turno"],
            "asignatura" => $datos["asignatura"],
            "nombreSolicitante" => $datos["nombreSolicitante"],
            "cedulaRegistrante" => $datos["documentoRegistrante"],
            "aulaId" => $datos["aulaId"],
        ]);

        return $this->conexion->lastInsertId();
    }

    /**
     * Registra un nuevo ticket asociado a una planilla, con el estado inicial de "pendiente".
     *
     * @param array $datos Array asociativo con las claves: descripcion, fallo, numeroPc,
     * aulaId, documentoRegistrante y planillaId.
     *
     * @return bool TRUE si la inserción se realiza correctamente, de lo contrario: FALSE .
     */
    public function registrarTicket(array $datos) {
        $sql = "INSERT INTO TICKET 
            (Descripcion, Fallo, Estado, FechaCreacion, PcNumPc, PcAulaID, SolicitanteCedula, PlanillaId)
            VALUES (:descripcion, :fallo, 'pendiente', NOW(), :pcNumPc, :pcAulaID, :solicitanteCedula, :planillaId)";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            "descripcion" => $datos["descripcion"],
            "fallo" => $datos["fallo"],
            "pcNumPc" => $datos["numeroPc"],
            "pcAulaID" => $datos["aulaId"],
            "solicitanteCedula" => $datos["documentoRegistrante"],
            "planillaId" => $datos["planillaId"],
        ]);
    }

 /**
     * Lista todos los tickets asociados a una planilla, ordenados por fecha de creación.
     *
     * @param int $planillaId ID de la planilla de la cual se quieren obtener los tickets.
     *
     * @return array Array que asocia con los datos de cada ticket (ID, Descripcion, Fallo,
     * Estado, PcNumPc, FechaCreacion).
     */
    public function listarTicketsDePlanilla(int $planillaId) {
        $sql = "SELECT ID, Descripcion, Fallo, Estado, PcNumPc, FechaCreacion
                FROM TICKET
                WHERE PlanillaId = :planillaId 
                ORDER BY FechaCreacion";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["planillaId" => $planillaId]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

     /**
     * Lista todas las aulas del sistema, indicando si son de tipo "laboratorio" o "taller".
     *
     * @return array Array asociativo con el número y tipo de cada aula.
     */
    public function listarAulas(){
            $sql = "SELECT AULA.Numero, 'laboratorio' AS Tipo 
                    FROM AULA JOIN LABORATORIO ON AULA.ID = LABORATORIO.AulaID
                    UNION ALL
                    SELECT AULA.Numero, 'taller' AS Tipo 
                    FROM AULA JOIN TALLER ON AULA.ID = TALLER.AulaID";

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);//devuelve la consulta en un array asociativo es decir cada espacio esta vinculado a una clave
        }

        /**
     * Busca el ID de un aula a partir de su tipo y número.
     *
     * @param string $tipo Tipo de aula ("laboratorio" o "taller").
     * @param string $numero Número del aula para buscar.
     *
     * @return int|null El ID del aula si existe, null si el tipo es inválido o no se encuentra el aula.
     */
    public function buscarAulaId(string $tipo, string $numero) {
        if ($tipo === "laboratorio") {
            $sql = "SELECT AULA.ID 
                    FROM AULA 
                    JOIN LABORATORIO ON AULA.ID = LABORATORIO.AulaID 
                    WHERE AULA.Numero = :numero";
        } elseif ($tipo === "taller") {
            $sql = "SELECT AULA.ID 
                    FROM AULA 
                    JOIN TALLER ON AULA.ID = TALLER.AulaID 
                    WHERE AULA.Numero = :numero";
        } else {
            return null;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["numero" => $numero]);

        $fila = $consulta->fetch(PDO::FETCH_ASSOC); 

        if ($fila === false) {
            return null; 
        }

        return $fila["ID"];
    }

    /**
     * Lista todos los registros de planilla junto con los datos del aula utilizada,
     * ordenados por fecha y hora de entrada descendente.
     *
     * @return array Array asociativo con los datos de cada planilla (ID, Fecha, HoraEntrada,
     * HoraSalida, NombreSolicitante, AulaNumero, AulaTipo).
     */
    
    public function listarRegistroPlanilla(
        ?string $cedula = null,
        string $periodo = "todo"
    ): array {

        $sql = "SELECT
                    p.ID,
                    p.Fecha,
                    p.HoraEntrada,
                    p.HoraSalida,
                    p.Grupo,
                    p.Turno,
                    p.Asignatura,
                    p.NombreSolicitante,
                    p.CedulaRegistrante,
                    a.Numero AS AulaNumero,
                    CASE
                        WHEN l.AulaID IS NOT NULL
                        THEN 'laboratorio'
                        ELSE 'taller'
                    END AS AulaTipo
                FROM PLANILLA p
                INNER JOIN AULA a ON a.ID = p.AulaID
                LEFT JOIN LABORATORIO l ON l.AulaID = a.ID
                WHERE 1 = 1";

        $parametros = [];

        if ($cedula !== null) {
            $sql .= " AND p.CedulaRegistrante = :cedula";
            $parametros["cedula"] = $cedula;
        }

        switch ($periodo) {
            case "dia":
                $sql .= " AND p.Fecha = CURDATE()";
                break;

            case "semana":
                $sql .= " AND p.Fecha >= DATE_SUB(
                    CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY
                ) AND p.Fecha < DATE_ADD(
                    DATE_SUB(
                        CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY
                    ), INTERVAL 7 DAY
                )";
                break;

            case "mes":
                $sql .= " AND YEAR(p.Fecha) = YEAR(CURDATE())
                        AND MONTH(p.Fecha) = MONTH(CURDATE())";
                break;
        }

        $sql .= " ORDER BY p.Fecha DESC,
                            p.HoraEntrada DESC,
                            p.ID DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * Obtiene los datos de una planilla específica junto con los datos del aula utilizada.
     *
     * @param int $planillaId ID de la planilla a buscar.
     *
     * @return array|null Array asociativo con los datos de la planilla, o null si no existe.
     */
    
        public function obtenerPlanillaPorId(
            int $planillaId,
            ?string $cedula = null
        ): ?array {

            $sql = "SELECT
                        p.ID,
                        p.Fecha,
                        p.HoraEntrada,
                        p.HoraSalida,
                        p.Grupo,
                        p.Turno,
                        p.Asignatura,
                        p.NombreSolicitante,
                        p.CedulaRegistrante,
                        p.AulaID,
                        a.Numero AS AulaNumero,
                        CASE
                            WHEN l.AulaID IS NOT NULL
                            THEN 'laboratorio'
                            ELSE 'taller'
                        END AS AulaTipo
                    FROM PLANILLA p
                    INNER JOIN AULA a ON a.ID = p.AulaID
                    LEFT JOIN LABORATORIO l ON l.AulaID = a.ID
                    WHERE p.ID = :planillaId";

            $parametros = ["planillaId" => $planillaId];

            if ($cedula !== null) {
                $sql .= " AND p.CedulaRegistrante = :cedula";
                $parametros["cedula"] = $cedula;
            }

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute($parametros);

            $fila = $consulta->fetch(PDO::FETCH_ASSOC);

            return $fila === false ? null : $fila;
        }


    
        public function existePcEnAula(
            string $numPc,
            int $aulaId
        ): bool {

            $sql = "SELECT 1
                    FROM PC
                    WHERE NumPc = :numPc
                    AND AulaID = :aulaId
                    LIMIT 1";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "numPc" => $numPc,
                "aulaId" => $aulaId
            ]);

            return $consulta->fetchColumn() !== false;
        }

}


 

 



?>