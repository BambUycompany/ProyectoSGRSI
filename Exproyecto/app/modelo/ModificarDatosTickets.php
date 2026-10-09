<?php
class ModificarDatosTickets{
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }
    public function cambiarEstadoTicket(int $id, string $nuevoEstado): bool {
        $estadosPermitidos = ['pendiente', 'en proceso', 'finalizado'];
        if (!in_array($nuevoEstado, $estadosPermitidos, true)) {
            return false;
        }

        $sql = "UPDATE TICKET SET Estado = :estado WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            'estado' => $nuevoEstado,
            'id' => $id
        ]);
    }

    public function cambiarPrioridadTicket(int $id, string $nuevaPrioridad): bool {
        $prioridadesPermitidas = ['alta', 'media', 'baja'];
        if (!in_array($nuevaPrioridad, $prioridadesPermitidas, true)) {
            return false;
        }

        $sql = "UPDATE TICKET SET Prioridad = :prioridad WHERE ID = :id";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            'prioridad' => $nuevaPrioridad,
            'id' => $id
        ]);
    }

    public function finalizarTicket(int $id, string $diagnostico, string $soporteCedula): bool {
        $sql = "UPDATE TICKET 
                SET Estado = 'finalizado', 
                    Incidente = :diagnostico, 
                    SoporteCedula = :soporteCedula 
                WHERE ID = :id";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            'diagnostico' => $diagnostico,
            'soporteCedula' => $soporteCedula,
            'id' => $id
        ]);
    }
}
?> 
