<?php

class Tesoreria extends Conexion
{
    public function listar(): array
    {
        $sql = "SELECT
                    tm.id,
                    tm.fecha,
                    tm.tipo_movimiento,
                    tc.nombre AS categoria,
                    tm.categoria_id,
                    tm.concepto,
                    tm.valor,
                    tm.medio_pago,
                    tm.numero_soporte,
                    tm.observaciones
                FROM tesoreria_movimientos tm
                INNER JOIN tesoreria_categorias tc ON tc.id = tm.categoria_id
                ORDER BY tm.fecha DESC, tm.id DESC";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardar(array $data, int $id = 0): int
    {
        if ($id > 0) {
            return $this->actualizar($data, $id);
        }

        return $this->insertar($data);
    }

    private function insertar(array $data): int
    {
        $sql = "INSERT INTO tesoreria_movimientos
                (
                    fecha,
                    tipo_movimiento,
                    categoria_id,
                    concepto,
                    valor,
                    medio_pago,
                    numero_soporte,
                    observaciones
                )
                VALUES
                (
                    :fecha,
                    :tipo_movimiento,
                    :categoria_id,
                    :concepto,
                    :valor,
                    :medio_pago,
                    :numero_soporte,
                    :observaciones
                )
                RETURNING id";

        $stmt = $this->conectar()->prepare($sql);

        $stmt->execute([
            ':fecha' => $data['fecha'],
            ':tipo_movimiento' => $data['tipo_movimiento'],
            ':categoria_id' => $data['categoria_id'],
            ':concepto' => $data['concepto'],
            ':valor' => $data['valor'],
            ':medio_pago' => $data['medio_pago'] !== '' ? $data['medio_pago'] : null,
            ':numero_soporte' => $data['numero_soporte'] !== '' ? $data['numero_soporte'] : null,
            ':observaciones' => $data['observaciones'] !== '' ? $data['observaciones'] : null,
        ]);

        return (int) $stmt->fetchColumn();
    }

    private function actualizar(array $data, int $id): int
    {
        $sql = "UPDATE tesoreria_movimientos
                SET
                    fecha = :fecha,
                    tipo_movimiento = :tipo_movimiento,
                    categoria_id = :categoria_id,
                    concepto = :concepto,
                    valor = :valor,
                    medio_pago = :medio_pago,
                    numero_soporte = :numero_soporte,
                    observaciones = :observaciones
                WHERE id = :id";

        $stmt = $this->conectar()->prepare($sql);

        $stmt->execute([
            ':fecha' => $data['fecha'],
            ':tipo_movimiento' => $data['tipo_movimiento'],
            ':categoria_id' => $data['categoria_id'],
            ':concepto' => $data['concepto'],
            ':valor' => $data['valor'],
            ':medio_pago' => $data['medio_pago'] !== '' ? $data['medio_pago'] : null,
            ':numero_soporte' => $data['numero_soporte'] !== '' ? $data['numero_soporte'] : null,
            ':observaciones' => $data['observaciones'] !== '' ? $data['observaciones'] : null,
            ':id' => $id,
        ]);

        return $id;
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT
                    id,
                    fecha,
                    tipo_movimiento,
                    categoria_id,
                    concepto,
                    valor,
                    medio_pago,
                    numero_soporte,
                    observaciones
                FROM tesoreria_movimientos
                WHERE id = :id";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
        ]);

        $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);

        return $movimiento ?: null;
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM tesoreria_movimientos
                WHERE id = :id";

        $stmt = $this->conectar()->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
        ]);
    }

    public function listarCategoriasPorTipo(string $tipoMovimiento): array
    {
        $sql = "SELECT
                    id,
                    nombre
                FROM tesoreria_categorias
                WHERE tipo_movimiento = :tipo_movimiento
                AND activa = true
                ORDER BY nombre ASC";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute([
            ':tipo_movimiento' => $tipoMovimiento,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function resumen(): array
    {
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN tipo_movimiento = 'entrada' THEN valor ELSE 0 END), 0) AS total_entradas,
                    COALESCE(SUM(CASE WHEN tipo_movimiento = 'salida' THEN valor ELSE 0 END), 0) AS total_salidas,
                    COALESCE(SUM(CASE WHEN tipo_movimiento = 'entrada' THEN valor ELSE 0 END), 0)
                    -
                    COALESCE(SUM(CASE WHEN tipo_movimiento = 'salida' THEN valor ELSE 0 END), 0) AS saldo
                FROM tesoreria_movimientos";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute();

        $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resumen ?: [
            'total_entradas' => 0,
            'total_salidas' => 0,
            'saldo' => 0,
        ];
    }
}
