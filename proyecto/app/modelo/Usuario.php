<?php

class Usuario {
    private string $cedula;
    private string $claveHash;
    private bool $activo;
    private bool $administrador;
    private bool $soporte;
    private bool $solicitante;

    public function __construct(string $cedula, string $claveHash, bool $activo, bool $administrador, bool $soporte, bool $solicitante) {
        $this->cedula = $cedula;
        $this->claveHash = $claveHash;
        $this->activo = $activo;
        $this->administrador = $administrador;
        $this->soporte = $soporte;
        $this->solicitante = $solicitante;
    }

    public function getCedula(): string {
        return $this->cedula;
    }

    public function getClaveHash(): string {
        return $this->claveHash;
    }

    public function estaActivo(): bool {
        return $this->activo;
    }

    public function esAdministrador(): bool {
        return $this->administrador;
    }

    public function esSolicitante(): bool{
        return $this->solicitante;
    }

    public function esSoporte(): bool {
        return $this->soporte;
    }
}

?>