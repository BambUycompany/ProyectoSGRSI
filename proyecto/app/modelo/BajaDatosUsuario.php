<?php


/**
 * Clase encargada de realizar operaciones de baja
 * relacionadas con los empleados del sistema.
 * 
 * AVISO: En un sistema real, la baja de un usuario es lógica, es decir, no se elmina, sino que se modifica su
 * estado a inactivo.
 * Por ser el único ejemplo de la realidad, en este caso se elimina de la base de datos con DELETE,
 * el caso ideal es modificar un atributo "estado" mediante UPDATE.
 */
class BajaDatosUsuario {
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
     * Elimina un usuario y sus relaciones dentro del sistema.
     *
     * @param string $cedula Cédula del usuario.
     *
     * @return bool TRUE si la operación se completa correctamente, FALSE en caso contrario.
     */
    public function eliminarUsuario(string $cedula): bool {

        try {
            $this->conexion->beginTransaction();

            $sqlRol = "DELETE FROM rol WHERE cedula = :cedula";
            $consultaRol = $this->conexion->prepare($sqlRol);
            $consultaRol->execute(["cedula" => $cedula]);

            $sqlSoporte = "DELETE FROM soporte WHERE cedula = :cedula";
            $consultaSoporte = $this->conexion->prepare($sqlSoporte);
            $consultaSoporte->execute(["cedula" => $cedula]);

            $sqlSolicitante = "DELETE FROM solicitante WHERE cedula = :cedula";
            $consultaSolicitante = $this->conexion->prepare($sqlSolicitante);
            $consultaSolicitante->execute(["cedula" => $cedula]);

            $sqlAdministrador = "DELETE FROM ADMINISTRADOR WHERE cedula = :cedula";
            $consultaAdministrador = $this->conexion->prepare($sqlAdministrador);
            $consultaAdministrador->execute(["cedula" => $cedula]);

            $sqlUsuario = "DELETE FROM USUARIO WHERE cedula = :cedula";
            $consultaUsuario = $this->conexion->prepare($sqlUsuario);
            $consultaUsuario->execute(["cedula" => $cedula]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }
}
?>