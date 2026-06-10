<?php
declare(strict_types=1);

class Afiliado extends Conexion
{
    public function listar(): array
    {
        $sql = "SELECT id, numero_afiliado, fecha_afiliacion, nombres_completos, edad, numero_identificacion,
                       tipo_identificacion, direccion, comite_trabajo, telefono, estado_afiliacion,
                       acta_fallo_edicto, meses_sancion, observaciones,
                       creado_en, actualizado_en,
                       to_char(fecha_afiliacion, 'YYYY-MM-DD') AS fecha_afiliacion_texto,
                       to_char(creado_en, 'YYYY-MM-DD HH24:MI') AS creado
                FROM afiliados
                ORDER BY id DESC";

        return $this->conectar()->query($sql)->fetchAll();
    }

    public function contar(): int
    {
        return (int) $this->conectar()->query('SELECT COUNT(*) FROM afiliados')->fetchColumn();
    }

    public function listarOpcionesAsistencia(): array
    {
        $sql = "SELECT id, numero_afiliado, nombres_completos, numero_identificacion, estado_afiliacion
                FROM afiliados
                ORDER BY nombres_completos ASC";

        return $this->conectar()->query($sql)->fetchAll();
    }

    public function obtener(int $id): ?array
    {
        $stmt = $this->conectar()->prepare('SELECT * FROM afiliados WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $afiliado = $stmt->fetch();

        return $afiliado ?: null;
    }

    public function crear(array $data): int
    {
        $sql = 'INSERT INTO afiliados
                    (numero_afiliado, fecha_afiliacion, nombres_completos, edad, numero_identificacion,
                     tipo_identificacion, direccion, comite_trabajo, telefono, estado_afiliacion,
                     acta_fallo_edicto, meses_sancion, observaciones)
                VALUES
                    (:numero_afiliado, :fecha_afiliacion, :nombres_completos, :edad, :numero_identificacion,
                     :tipo_identificacion, :direccion, :comite_trabajo, :telefono, :estado_afiliacion,
                     :acta_fallo_edicto, :meses_sancion, :observaciones)
                RETURNING id';

        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute($data);

        return (int) $stmt->fetchColumn();
    }

    public function actualizar(int $id, array $data): bool
    {
        $data['id'] = $id;

        $sql = 'UPDATE afiliados
                SET numero_afiliado = :numero_afiliado,
                    fecha_afiliacion = :fecha_afiliacion,
                    nombres_completos = :nombres_completos,
                    edad = :edad,
                    numero_identificacion = :numero_identificacion,
                    tipo_identificacion = :tipo_identificacion,
                    direccion = :direccion,
                    comite_trabajo = :comite_trabajo,
                    telefono = :telefono,
                    estado_afiliacion = :estado_afiliacion,
                    acta_fallo_edicto = :acta_fallo_edicto,
                    meses_sancion = :meses_sancion,
                    observaciones = :observaciones,
                    actualizado_en = CURRENT_TIMESTAMP
                WHERE id = :id';

        $stmt = $this->conectar()->prepare($sql);

        return $stmt->execute($data);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->conectar()->prepare('DELETE FROM afiliados WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}
