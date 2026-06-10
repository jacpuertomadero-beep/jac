<?php
declare(strict_types=1);

class Comunicacion extends Conexion
{
    public function listar(): array
    {
        $sql = "SELECT c.id, c.tipo_comunicacion, c.asunto, c.tercero, c.fecha_radicado,
                       c.numero_radicado, c.medio_radicacion, c.url_drive_comunicacion,
                       c.seguimiento, c.url_drive_seguimiento, c.fecha_respuesta,
                       c.respuesta, c.url_drive_respuesta, c.observaciones,
                       c.creado_en, c.actualizado_en,
                       to_char(c.fecha_radicado, 'YYYY-MM-DD') AS fecha_radicado_texto,
                       to_char(c.fecha_respuesta, 'YYYY-MM-DD') AS fecha_respuesta_texto,
                       CASE
                           WHEN c.fecha_respuesta IS NOT NULL
                                OR NULLIF(BTRIM(COALESCE(c.respuesta, '')), '') IS NOT NULL
                                OR NULLIF(BTRIM(COALESCE(c.url_drive_respuesta, '')), '') IS NOT NULL
                           THEN TRUE
                           ELSE FALSE
                       END AS tiene_respuesta,
                       (
                           SELECT COUNT(*)::INTEGER
                           FROM generate_series(
                               c.fecha_radicado + INTERVAL '1 day',
                               COALESCE(c.fecha_respuesta, CURRENT_DATE),
                               INTERVAL '1 day'
                           ) AS dias(fecha)
                           WHERE EXTRACT(ISODOW FROM dias.fecha) BETWEEN 1 AND 5
                       ) AS dias_habiles_transcurridos
                FROM comunicaciones c
                ORDER BY c.fecha_radicado DESC, c.id DESC";

        return $this->conectar()->query($sql)->fetchAll();
    }

    public function obtener(int $id): ?array
    {
        $stmt = $this->conectar()->prepare('SELECT * FROM comunicaciones WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $comunicacion = $stmt->fetch();

        return $comunicacion ?: null;
    }

    public function crear(array $data): int
    {
        $sql = 'INSERT INTO comunicaciones
                    (tipo_comunicacion, asunto, tercero, fecha_radicado, numero_radicado,
                     medio_radicacion, url_drive_comunicacion, seguimiento, url_drive_seguimiento,
                     fecha_respuesta, respuesta, url_drive_respuesta, observaciones)
                VALUES
                    (:tipo_comunicacion, :asunto, :tercero, :fecha_radicado, :numero_radicado,
                     :medio_radicacion, :url_drive_comunicacion, :seguimiento, :url_drive_seguimiento,
                     :fecha_respuesta, :respuesta, :url_drive_respuesta, :observaciones)
                RETURNING id';

        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute($data);

        return (int) $stmt->fetchColumn();
    }

    public function actualizar(int $id, array $data): bool
    {
        $data['id'] = $id;

        $sql = 'UPDATE comunicaciones
                SET tipo_comunicacion = :tipo_comunicacion,
                    asunto = :asunto,
                    tercero = :tercero,
                    fecha_radicado = :fecha_radicado,
                    numero_radicado = :numero_radicado,
                    medio_radicacion = :medio_radicacion,
                    url_drive_comunicacion = :url_drive_comunicacion,
                    seguimiento = :seguimiento,
                    url_drive_seguimiento = :url_drive_seguimiento,
                    fecha_respuesta = :fecha_respuesta,
                    respuesta = :respuesta,
                    url_drive_respuesta = :url_drive_respuesta,
                    observaciones = :observaciones,
                    actualizado_en = CURRENT_TIMESTAMP
                WHERE id = :id';

        $stmt = $this->conectar()->prepare($sql);

        return $stmt->execute($data);
    }
}
