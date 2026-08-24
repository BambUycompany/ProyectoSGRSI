<?php

/**
 * Clase encargada de la lógica en el proceso de autenticación de usuarios.
 *
 * Delega en AccesoDatosUsuario la recuperación de datos y se solo
 * valida el estado de la cuenta actual y la contraseña.
 */
class Login {
    private AccesoDatosUsuario $accesoDatosUsuario;

/**
     * Constructor que recibe el objeto de acceso a datos de usuario.
     *
     * @param AccesoDatosUsuario $accesoDatosUsuario Objeto encargado de buscar usuarios. PRECONDICIÓN: No debe ser NULL.
     */
    public function __construct(AccesoDatosUsuario $accesoDatosUsuario) {
        $this->accesoDatosUsuario = $accesoDatosUsuario;
    }


     /**
     * Autentica a un usuario al validar sus credenciales, como: cédula, estado de cuenta y contraseña.
     *
     * @param string $cedula Cédula del usuario que intenta iniciar sesión.
     * @param string $password Contraseña ingresada por el usuario.
     *
     * @return Usuario|null El objeto Usuario si la autenticación es exitosa, null si el usuario no existe,
     * la contraseña es incorrecta.
     */
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