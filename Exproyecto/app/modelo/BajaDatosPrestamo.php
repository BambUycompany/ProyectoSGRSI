<?php
class BajaDatosPrestamo {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Solo permite eliminar préstamos ya devueltos, para no perder el rastro
     * de un portátil que en teoría todavía está afuera.
     */
    public function eliminarPrestamo(int $prestamoId): bool {
        $sql = "DELETE FROM PRESTAMO WHERE ID = :id AND Estado = 'devuelto'";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["id" => $prestamoId]);
        return $consulta->rowCount() > 0;
    }
}

?>