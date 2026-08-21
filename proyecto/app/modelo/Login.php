<?php

class Login {
    private AccesoDatosUsuario $accesoDatosUsuario;

    public function __construct(AccesoDatosUsuario $accesoDatosUsuario) {
        $this->accesoDatosUsuario = $accesoDatosUsuario;
    }

    public function autenticar(string $cedula, string $password): ?Usuario {
        $usuario = $this->accesoDatosUsuario->buscarUsuario($cedula);

        if ($usuario === null) {
            return null;
        }

        if (!$usuario->estaActivo()) {
            return null;
        }

        if ( !password_verify($password, $usuario->getClaveHash() ) ){
            return null;
        }

        return $usuario;
    }
}

?>