<?php
//Instalación del driver https://www.php.net/manual/en/pdo.installation.php
//LEER ATENTAMENTE CÓMO SE CONFIGURA TANTO EN LINUX COMO EN WINDOWS
//Especificar en php.ini el extension_dir (debe apuntar a ext) y la extension pdo_mysql para este caso

/**
 * Clase encargada de gestionar la conexión a la base de datos mediante PDO.
 *
 * Concentra los datos de conexión (servidor, usuario, contraseña y base de datos)
 * y expone métodos para iniciar y cerrar la conexión.
 */
class ConectorPDO
{
    private string $servername;
    private string $username;
    private string $password;
    private string $dbname;
    private ?PDO $conexion;


    /**
     * Constructor que recibe los datos necesarios para la conexion.
     *
     * @param string $servername Dirección del servidor de base de datos.
     * @param string $username Usuario de la base de datos.
     * @param string $password Contraseña del usuario de la base de datos.
     * @param string $dbname Nombre de la base de datos a la que se conecta.
     */
    public function __construct (string $servername, string $username, string $password, string $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->conexion = null;
    }


    /**
     * Establece la conexión con la base de datos utilizando los datos en el constructor.
     *
     * En caso de que se presnte un error, el mensaje se imprime directamente 
     * y el método retorna la conexión, la cual quedará en null si la creación del PDO falló.
     *
     * @return PDO La conexión PDO ya establecida, configurada para lanzar excepciones ante errores.
     */
    public function establecerConexion(): PDO {
        try {
            $this->conexion = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            // set the PDO error mode to exception
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error al conectar..." . $e->getMessage();
        }
        return $this->conexion;
    }

 /**
     * Cierra la conexión actual con la base de datos.
     */
    public function desconectar() {
        $this->conexion = null;
    }
};

//Código para depuración
//$ConectorPDO = new ConectorPDO ("localhost:3306", "leandro", "123", "test");
//$ConectorPDO->establecerConexion();

?>