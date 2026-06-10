<?php

declare(strict_types=1);

class Asamblea extends Conexion
{
    public function listar(): array
    {
        $sql = "SELECT a.id, a.numero_acta, a.fecha_asamblea, a.observaciones,
                       a.creado_en, a.actualizado_en,
                       to_char(a.fecha_asamblea, 'YYYY-MM-DD') AS fecha_asamblea_texto,
                       COUNT(aa.afiliado_id)::INTEGER AS asistentes,
                       total_afiliados.total::INTEGER AS total_afiliados,
                       CASE
                           WHEN total_afiliados.total = 0 THEN 0
                           ELSE ROUND((COUNT(aa.afiliado_id)::NUMERIC / total_afiliados.total::NUMERIC) * 100, 2)
                       END AS porcentaje_participacion
                FROM actas_asamblea a
                LEFT JOIN asamblea_asistencias aa ON aa.acta_id = a.id
                CROSS JOIN (SELECT COUNT(*) AS total FROM afiliados) total_afiliados
                GROUP BY a.id, total_afiliados.total
                ORDER BY a.fecha_asamblea DESC, a.id DESC";

        return $this->conectar()->query($sql)->fetchAll();
    }

    public function obtener(int $id): ?array
    {
        $stmt = $this->conectar()->prepare('SELECT * FROM actas_asamblea WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $acta = $stmt->fetch();

        if (!$acta) {
            return null;
        }

        $acta['afiliados'] = $this->obtenerAfiliadosActa($id);

        return $acta;
    }

    public function guardar(array $data, array $afiliadoIds, int $id = 0): int
    {
        $pdo = $this->conectar();
        $pdo->beginTransaction();

        try {
            if ($id > 0) {
                $sql = 'UPDATE actas_asamblea
                        SET numero_acta = :numero_acta,
                            fecha_asamblea = :fecha_asamblea,
                            observaciones = :observaciones,
                            actualizado_en = CURRENT_TIMESTAMP
                        WHERE id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'numero_acta' => $data['numero_acta'],
                    'fecha_asamblea' => $data['fecha_asamblea'],
                    'observaciones' => $data['observaciones'],
                    'id' => $id,
                ]);

                $actaId = $id;
                $pdo->prepare('DELETE FROM asamblea_asistencias WHERE acta_id = :acta_id')
                    ->execute(['acta_id' => $actaId]);
            } else {
                $sql = 'INSERT INTO actas_asamblea (numero_acta, fecha_asamblea, observaciones)
                        VALUES (:numero_acta, :fecha_asamblea, :observaciones)
                        RETURNING id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute($data);
                $actaId = (int) $stmt->fetchColumn();
            }

            $this->guardarAsistentes($pdo, $actaId, $afiliadoIds);
            $pdo->commit();

            return $actaId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function listarAfiliadosActivosAsistencia(): array
    {
        $sql = "SELECT 
                id,
                numero_afiliado,
                nombres_completos,
                numero_identificacion,
                telefono,
                comite_trabajo
            FROM afiliados
            WHERE estado_afiliacion = 'afiliado'
            ORDER BY nombres_completos ASC";

        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenQuorum(): array
    {
        $sql = "SELECT COUNT(*) AS total FROM afiliados WHERE estado_afiliacion = 'afiliado'";
        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute();

        $total = (int) $stmt->fetchColumn();

        return [
            'total_activos' => $total,
            'mitad_mas_uno' => floor($total / 2) + 1,
            'treinta_por_ciento' => ceil($total * 0.30),
            'veinte_por_ciento' => ceil($total * 0.20),
        ];
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->conectar()->prepare('DELETE FROM actas_asamblea WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    private function guardarAsistentes(PDO $pdo, int $actaId, array $afiliadoIds): void
    {
        if (!$afiliadoIds) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO asamblea_asistencias (acta_id, afiliado_id)
             VALUES (:acta_id, :afiliado_id)'
        );

        foreach ($afiliadoIds as $afiliadoId) {
            $stmt->execute([
                'acta_id' => $actaId,
                'afiliado_id' => $afiliadoId,
            ]);
        }
    }

    private function obtenerAfiliadosActa(int $actaId): array
    {
        $stmt = $this->conectar()->prepare(
            'SELECT afiliado_id
             FROM asamblea_asistencias
             WHERE acta_id = :acta_id
             ORDER BY afiliado_id'
        );
        $stmt->execute(['acta_id' => $actaId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}
