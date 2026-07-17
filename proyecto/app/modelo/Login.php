<?php

class Login {
    private consultaUsuario $consultaUsuario;

    public function __construct(consultaUsuario $consultaUsuario) {
        $this->consultaUsuario = $consultaUsuario;
    }

    public function autenticar(string $cedula, string $clave): ?Usuario {
        $usuario = $this->consultaUsuario->buscarUsuario($cedula);

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