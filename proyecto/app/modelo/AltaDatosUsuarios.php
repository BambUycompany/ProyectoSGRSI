<?php


/**
 * Clase encargada de dar de alta usuarios y de listarlos dentro del sistema.
 */
class AltaDatosUsuarios {
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
     * Registra un nuevo usuario y su correspondiente rol en el sistema.
     *
     * Primero inserta al usuario y luego, según el rol recibido, lo registra
     *  en la tabla del rol que corresponde (administrador, soporte
     * o solicitante). Si el rol no es válido, se revierted la transacción.
     *
     * @param string $cedula Cédula del usuario a registrar.
     * @param string $nombre Nombre del usuario.
     * @param string $apellido Apellido del usuario.
     * @param string $claveHash Hash de la contraseña del usuario.
     * @param string $rol Rol a asignar ("administrador", "soporte" o "solicitante").
     *
     * @return bool TRUE si el registro se realiza correctamente, FALSE en caso contrario.
     */
    public function registrarUsuario(string $cedula, string $nombre, string $apellido, string $claveHash, string $rol) {

        try{

            $this->conexion->beginTransaction();

            $sqlUsuario = "INSERT INTO usuario (cedula, nombre, apellido, claveHash) VALUES (:cedula, :nombre, :apellido, :claveHash)";

            $consultaUsuario = $this->conexion->prepare($sqlUsuario);

            $consultaUsuario->execute(["cedula" => $cedula, "nombre" => $nombre, "apellido" => $apellido, "claveHash" => $claveHash]);

            switch ($rol) {
                case "administrador":
                    $sqlRol = "INSERT INTO administrador (cedula) VALUES (:cedula)";
                    break;
                case "soporte":
                    $sqlRol = "INSERT INTO soporte (cedula) VALUES (:cedula)";
                    break;
                case "solicitante":
                    $sqlRol = "INSERT INTO solicitante (cedula) VALUES (:cedula)";
                    break;
                default:
                    $this->conexion->rollBack();
                    return false;
            }
            
            $consultaRol = $this->conexion->prepare($sqlRol);

            $consultaRol->execute(["cedula" => $cedula]);

            $this->conexion->commit();

            return true;
        
        }catch (PDOException $e) {
            
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
        return false;
        }

       
    }

     /**
     * Lista todos los usuarios registrados junto con los roles que tienen asignados.
     *
     * @return array Array asociado con los datos de cada usuario (cedula, nombre,
     * apellido y banderas administrador/soporte/solicitante).
     */
    public function listarUsuarios() {
    $sql = "SELECT 
                u.cedula, 
                u.nombre, 
                u.apellido,
                CASE WHEN a.cedula IS NOT NULL THEN 1 ELSE 0 END AS administrador,
                CASE WHEN so.cedula IS NOT NULL THEN 1 ELSE 0 END AS soporte,
                CASE WHEN s.cedula IS NOT NULL THEN 1 ELSE 0 END AS solicitante
            FROM usuario u
            LEFT JOIN administrador a ON a.cedula = u.cedula
            LEFT JOIN soporte so ON so.cedula = u.cedula
            LEFT JOIN solicitante s ON s.cedula = u.cedula
            ORDER BY u.nombre";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}


}
   
?>