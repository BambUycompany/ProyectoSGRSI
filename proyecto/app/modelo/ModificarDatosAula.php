<?php
class ModificarDatosAula {

    private PDO $conexion;


        /**
         * Constructor parametrizado que recibe una conexión
         * a la base de datos.
         *
         * @param PDO $conexion Conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
         */
        public function __construct(PDO $conexion)
        {
            $this->conexion = $conexion;
        }


        /**
         * Modifica los datos de un usuario y su rol.
         *
         * @param string $cedula Cédula del usuario a modificar.
         * @param string $nombre Nuevo nombre.
         * @param string $apellido Nuevo apellido.
         * @param string $claveHash Nuevo hash de contraseña.
         * @param string $rol Rol seleccionado.
         *
         * @return bool TRUE si la modificación se realiza correctamente, FALSE en caso contrario.
         */
        public function modificarAula(string $tiṕo, string $numero): bool {
            try {
                $this->conexion->beginTransaction();

                $sqlUsuario = "UPDATE AULA SET tipo = :tipo, numero = :numero";
                $consultaAula = $this->conexion->prepare($sqlAula);
                $consultaUsuario->execute(["tipo" => $tipo, "numero" => $numero]);

                switch ($tipo) {
                    case "laboratorio":
                        
                        $sqlRol = "DELETE FROM rol WHERE cedula = :cedula";
                        $consultaRol = $this->conexion->prepare($sqlRol);
                        $consultaRol->execute(["cedula" => $cedula]);

                        
                        $sqlSoporte = "DELETE FROM soporte WHERE cedula = :cedula";
                        $consultaSoporte = $this->conexion->prepare($sqlSoporte);
                        $consultaSoporte->execute(["cedula" => $cedula]);

                        $sqlSolicitante = "DELETE FROM solicitante WHERE cedula = :cedula";
                        $consultaSolicitante = $this->conexion->prepare($sqlSolicitante);
                        $consultaSolicitante->execute(["cedula" => $cedula]);

                        //Comprueba si ya es Administrador
                        $sqlBuscarAdministrador = "SELECT cedula FROM ADMINISTRADOR WHERE cedula = :cedula";
                        $consultaBuscarAdministrador = $this->conexion->prepare($sqlBuscarAdministrador);
                        $consultaBuscarAdministrador->execute(["cedula" => $cedula]);
                        $administrador = $consultaBuscarAdministrador->fetch(PDO::FETCH_ASSOC);

                        //Si todavía no era Administrador, registra el nuevo rol
                        if ($administrador === false) {
                            $sqlAdministrador = " INSERT INTO ADMINISTRADOR (cedula) VALUES (:cedula)";
                            $consultaAdministrador = $this->conexion->prepare($sqlAdministrador);
                            $consultaAdministrador->execute(["cedula" => $cedula]);
                        }
                        break;
                    case "Soporte":
                        //Elimina el posible rol Administrador
                        $sqlAdministrador = "DELETE FROM ADMINISTRADOR WHERE cedula = :cedula";
                        $consultaAdministrador = $this->conexion->prepare($sqlAdministrador);
                        $consultaAdministrador->execute(["cedula" => $cedula]);

                        $sqlSolicitante = "DELETE FROM solicitante WHERE cedula = :cedula";
                        $consultaSolicitante = $this->conexion->prepare($sqlSolicitante);
                        $consultaSolicitante->execute(["cedula" => $cedula]);

                        //Comprueba si ya pertenece a Soporte
                        $sqlBuscarSoporte = "SELECT cedula FROM soporte WHERE cedula = :cedula";
                        $consultaBuscarSoporte = $this->conexion->prepare($sqlBuscarSoporte);
                        $consultaBuscarSoporte->execute(["cedula" => $cedula]);
                        $soporte = $consultaBuscarSoporte->fetch(PDO::FETCH_ASSOC);

                        //Si todavía no era Soporte, registra el nuevo rol
                        if ($soporte === false) {
                            $sqlSoporte = "INSERT INTO soporte (cedula) VALUES (:cedula)";
                            $consultaSoporte = $this->conexion->prepare($sqlSoporte);
                            $consultaSoporte->execute(["cedula" => $cedula]);
                        }

                        break;
                    case "Solicitante":
                        //Elimina el posible rol Administrador
                        $sqlAdministrador = "DELETE FROM ADMINISTRADOR WHERE cedula = :cedula";
                        $consultaAdministrador = $this->conexion->prepare($sqlAdministrador);
                        $consultaAdministrador->execute(["cedula" => $cedula]);

                        $sqlSoporte = "DELETE FROM soporte WHERE cedula = :cedula";
                        $consultaSoporte = $this->conexion->prepare($sqlSoporte);
                        $consultaSoporte->execute(["cedula" => $cedula]);

                        //Comprueba si ya pertenece a Solicitante
                        $sqlBuscarSolicitante = "SELECT cedula FROM solicitante WHERE cedula = :cedula";
                        $consultaBuscarSolicitante = $this->conexion->prepare($sqlBuscarSolicitante);
                        $consultaBuscarSolicitante->execute(["cedula" => $cedula]);
                        $solicitante = $consultaBuscarSolicitante->fetch(PDO::FETCH_ASSOC);

                        //Si todavía no era Solicitante, registra el nuevo rol
                        if ($solicitante === false) {
                            $sqlSolicitante = "INSERT INTO solicitante (cedula) VALUES (:cedula)";
                            $consultaSolicitante = $this->conexion->prepare($sqlSolicitante);
                            $consultaSolicitante->execute(["cedula" => $cedula]);
                        }

                        break;

                    default:
                        $this->conexion->rollBack();
                        return false;
                }

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