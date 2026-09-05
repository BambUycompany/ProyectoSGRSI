<?php

/**
 * Clase de entidad que representa a un usuario del sistema.
 *
 * Almacena los datos y credenciales del usuario, y a su vez sus roles
 * asignados (administrador, soporte, solicitante).
 */
class Usuario {
    private string $cedula;
    private string $claveHash;
    private bool $activo;
    private bool $administrador;
    private bool $soporte;
    private bool $solicitante;

     /**
     * Constructor que recibe todos los datos del usuario.
     *
     * @param string $cedula Cédula del usuario, que se utiliza como el identificador.
     * @param string $claveHash Hash de la contraseña del usuario.
     * @param bool $activo Indica si la cuenta del usuario está activa o no.
     * @param bool $administrador Indica si el usuario tiene el rol de administrador.
     * @param bool $soporte Indica si el usuario tiene el rol de soporte.
     * @param bool $solicitante Indica si el usuario tiene el rol de solicitante.
     */
    public function __construct(string $cedula, string $claveHash, bool $activo, bool $administrador, bool $soporte, bool $solicitante) {
        $this->cedula = $cedula;
        $this->claveHash = $claveHash;
        $this->activo = $activo;
        $this->administrador = $administrador;
        $this->soporte = $soporte;
        $this->solicitante = $solicitante;
    }

     /**
     * @return string La cédula del usuario.
     */
    public function getCedula(): string { 
        return $this->cedula;
    }

     /**
     * @return string El hash de la contraseña del usuario.
     */
    public function getClaveHash(): string {
        return $this->claveHash;
    }

      /**
     * @return bool TRUE si la cuenta del usuario está activa, FALSE en caso contrario.
     */
    public function estaActivo(): bool {
        return $this->activo;
    }

      /**
     * @return bool TRUE si el usuario tiene el rol de administrador, FALSE en caso contrario.
     */
    public function esAdministrador(): bool {
        return $this->administrador;
    }

    /**
     * @return bool TRUE si el usuario tiene el rol de solicitante, FALSE en caso contrario.
     */
    public function esSolicitante(): bool{
        return $this->solicitante;
    }

    /**
     * @return bool TRUE si el usuario tiene el rol de soporte, FALSE en caso contrario.
     */
    public function esSoporte(): bool {
        return $this->soporte;
    }
}

?>