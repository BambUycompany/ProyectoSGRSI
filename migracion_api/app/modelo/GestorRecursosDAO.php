<?php
class GestorRecursosDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    // ==========================================
    // SECCIÓN AULAS
    // ==========================================

    public function obtenerAulaPorId(int $aulaId): ?array {
        $consulta = $this->conexion->prepare("SELECT * FROM AULA WHERE id = :id");
        $consulta->execute(["id" => $aulaId]);
        $aula = $consulta->fetch(PDO::FETCH_ASSOC);
        return $aula ?: null;
    }

    public function listarAulas(): array {
        $consulta = $this->conexion->query("SELECT * FROM AULA");
        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function existeAula(string $tipo, string $numero): bool {
        $consulta = $this->conexion->prepare("SELECT COUNT(*) FROM AULA WHERE tipo = :tipo AND numero = :numero");
        $consulta->execute(["tipo" => $tipo, "numero" => $numero]);
        return (int)$consulta->fetchColumn() > 0;
    }

    public function crearAula(string $tipo, string $numero): bool {
        $consulta = $this->conexion->prepare("INSERT INTO AULA (tipo, numero) VALUES (:tipo, :numero)");
        return $consulta->execute(["tipo" => $tipo, "numero" => $numero]);
    }

    public function modificarAula(int $aulaId, string $tipo, string $numero): bool {
        $consulta = $this->conexion->prepare("UPDATE AULA SET tipo = :tipo, numero = :numero WHERE id = :id");
        return $consulta->execute(["tipo" => $tipo, "numero" => $numero, "id" => $aulaId]);
    }

    public function eliminarAula(int $aulaId): bool {
        $consulta = $this->conexion->prepare("DELETE FROM AULA WHERE id = :id");
        return $consulta->execute(["id" => $aulaId]);
    }

    // ==========================================
    // SECCIÓN EQUIPOS
    // ==========================================

    public function listarEquiposDeAula(int $aulaId): array {
        $consulta = $this->conexion->prepare("SELECT * FROM EQUIPO WHERE aula_id = :aulaId");
        $consulta->execute(["aulaId" => $aulaId]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function agregarEquipo(int $aulaId, string $numPc, string $modeloPc, string $monitor, string $modeloMouse, string $modeloTeclado): bool {
        $sql = "INSERT INTO EQUIPO (aula_id, num_pc, modelo_pc, monitor, modelo_mouse, modelo_teclado) 
                VALUES (:aulaId, :numPc, :modeloPc, :monitor, :modeloMouse, :modeloTeclado)";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            "aulaId" => $aulaId,
            "numPc" => $numPc,
            "modeloPc" => $modeloPc, 
            "monitor" => $monitor, 
            "modeloMouse" => $modeloMouse, 
            "modeloTeclado" => $modeloTeclado
        ]);
    }

    public function modificarEquipo(string $numPc, int $aulaId, string $modeloPc, string $monitor, string $modeloMouse, string $modeloTeclado): bool {
        $sql = "UPDATE EQUIPO 
                SET modelo_pc = :modeloPc, monitor = :monitor, modelo_mouse = :modeloMouse, modelo_teclado = :modeloTeclado 
                WHERE num_pc = :numPc AND aula_id = :aulaId";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            "numPc" => $numPc, 
            "aulaId" => $aulaId, 
            "modeloPc" => $modeloPc, 
            "monitor" => $monitor, 
            "modeloMouse" => $modeloMouse, 
            "modeloTeclado" => $modeloTeclado
        ]);
    }

    public function eliminarEquipo(string $numPc, int $aulaId): bool {
        $consulta = $this->conexion->prepare("DELETE FROM EQUIPO WHERE num_pc = :numPc AND aula_id = :aulaId");
        return $consulta->execute(["numPc" => $numPc, "aulaId" => $aulaId]);
    }

    // ==========================================
    // SECCIÓN PORTÁTILES
    // ==========================================

    public function listarPortatiles(): array {
        $consulta = $this->conexion->query("SELECT * FROM PORTATIL");
        return $consulta->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function crearPortatil(string $modelo): bool {
        $consulta = $this->conexion->prepare("INSERT INTO PORTATIL (modelo, estado) VALUES (:modelo, 'disponible')");
        return $consulta->execute(["modelo" => $modelo]);
    }

    public function modificarPortatil(int $portatilId, string $modelo): bool {
        $consulta = $this->conexion->prepare("UPDATE PORTATIL SET modelo = :modelo WHERE id = :id");
        return $consulta->execute(["modelo" => $modelo, "id" => $portatilId]);
    }

    public function eliminarPortatil(int $portatilId): bool {
        $consulta = $this->conexion->prepare("DELETE FROM PORTATIL WHERE id = :id");
        return $consulta->execute(["id" => $portatilId]);
    }

    public function deshabilitarPortatil(int $portatilId): bool {
        $consulta = $this->conexion->prepare("UPDATE PORTATIL SET estado = 'deshabilitado' WHERE id = :id AND estado = 'disponible'");
        $consulta->execute(["id" => $portatilId]);
        return $consulta->rowCount() > 0;
    }

    public function habilitarPortatil(int $portatilId): bool {
        $consulta = $this->conexion->prepare("UPDATE PORTATIL SET estado = 'disponible' WHERE id = :id");
        $consulta->execute(["id" => $portatilId]);
        return $consulta->rowCount() > 0;
    }
}
?>