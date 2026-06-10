<?php
declare(strict_types=1);

class Organizacion extends Conexion
{
    public function obtenerActual(): ?array
    {
        $pdo = $this->conectar();
        $sql = "SELECT id, nombre, nit, personeria_juridica, direccion, barrio_vereda,
                       municipio, departamento, telefono, email, periodo_inicio, periodo_fin,
                       numero_resolucion_dignatarios, fecha_resolucion_dignatarios,
                       url_drive_resolucion, observaciones, creado_en, actualizado_en,
                       to_char(periodo_inicio, 'YYYY-MM-DD') AS periodo_inicio_texto,
                       to_char(periodo_fin, 'YYYY-MM-DD') AS periodo_fin_texto,
                       to_char(fecha_resolucion_dignatarios, 'YYYY-MM-DD') AS fecha_resolucion_texto
                FROM organizacion_comunal
                ORDER BY id ASC
                LIMIT 1";

        $organizacion = $pdo->query($sql)->fetch();

        if (!$organizacion) {
            return null;
        }

        $organizacion['dignatarios'] = $this->listarDignatarios((int) $organizacion['id']);

        return $organizacion;
    }

    public function guardar(array $data, array $dignatarios, int $id = 0): int
    {
        $pdo = $this->conectar();
        $pdo->beginTransaction();

        try {
            $organizacionId = $id > 0 ? $id : $this->obtenerPrimerId($pdo);

            if ($organizacionId > 0) {
                $this->actualizar($pdo, $organizacionId, $data);
            } else {
                $organizacionId = $this->crear($pdo, $data);
            }

            $this->guardarDignatarios($pdo, $organizacionId, $dignatarios);
            $pdo->commit();

            return $organizacionId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private function obtenerPrimerId(PDO $pdo): int
    {
        return (int) $pdo->query('SELECT id FROM organizacion_comunal ORDER BY id ASC LIMIT 1')->fetchColumn();
    }

    private function crear(PDO $pdo, array $data): int
    {
        $sql = 'INSERT INTO organizacion_comunal
                    (nombre, nit, personeria_juridica, direccion, barrio_vereda, municipio,
                     departamento, telefono, email, periodo_inicio, periodo_fin,
                     numero_resolucion_dignatarios, fecha_resolucion_dignatarios,
                     url_drive_resolucion, observaciones)
                VALUES
                    (:nombre, :nit, :personeria_juridica, :direccion, :barrio_vereda, :municipio,
                     :departamento, :telefono, :email, :periodo_inicio, :periodo_fin,
                     :numero_resolucion_dignatarios, :fecha_resolucion_dignatarios,
                     :url_drive_resolucion, :observaciones)
                RETURNING id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $stmt->fetchColumn();
    }

    private function actualizar(PDO $pdo, int $id, array $data): void
    {
        $data['id'] = $id;

        $sql = 'UPDATE organizacion_comunal
                SET nombre = :nombre,
                    nit = :nit,
                    personeria_juridica = :personeria_juridica,
                    direccion = :direccion,
                    barrio_vereda = :barrio_vereda,
                    municipio = :municipio,
                    departamento = :departamento,
                    telefono = :telefono,
                    email = :email,
                    periodo_inicio = :periodo_inicio,
                    periodo_fin = :periodo_fin,
                    numero_resolucion_dignatarios = :numero_resolucion_dignatarios,
                    fecha_resolucion_dignatarios = :fecha_resolucion_dignatarios,
                    url_drive_resolucion = :url_drive_resolucion,
                    observaciones = :observaciones,
                    actualizado_en = CURRENT_TIMESTAMP
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }

    private function guardarDignatarios(PDO $pdo, int $organizacionId, array $dignatarios): void
    {
        $stmtEliminar = $pdo->prepare('DELETE FROM organizacion_dignatarios WHERE organizacion_id = :organizacion_id');
        $stmtEliminar->execute(['organizacion_id' => $organizacionId]);

        $sql = 'INSERT INTO organizacion_dignatarios
                    (organizacion_id, bloque, cargo, afiliado_id)
                VALUES
                    (:organizacion_id, :bloque, :cargo, :afiliado_id)';
        $stmt = $pdo->prepare($sql);

        foreach ($dignatarios as $dignatario) {
            $stmt->execute([
                'organizacion_id' => $organizacionId,
                'bloque' => $dignatario['bloque'],
                'cargo' => $dignatario['cargo'],
                'afiliado_id' => $dignatario['afiliado_id'],
            ]);
        }
    }

    private function listarDignatarios(int $organizacionId): array
    {
        $sql = "SELECT od.id, od.bloque, od.cargo, od.afiliado_id,
                       a.numero_afiliado, a.nombres_completos, a.numero_identificacion,
                       a.estado_afiliacion
                FROM organizacion_dignatarios od
                INNER JOIN afiliados a ON a.id = od.afiliado_id
                WHERE od.organizacion_id = :organizacion_id
                ORDER BY od.bloque ASC, od.cargo ASC";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute(['organizacion_id' => $organizacionId]);

        $dignatarios = [];
        foreach ($stmt->fetchAll() as $dignatario) {
            $dignatarios[$dignatario['cargo']] = $dignatario;
        }

        return $dignatarios;
    }
}
