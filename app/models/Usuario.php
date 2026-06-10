<?php
declare(strict_types=1);

class Usuario extends Conexion
{
    public function buscarPorEmail(string $email): ?array
    {
        $sql = 'SELECT id, nombres, email, password_hash, rol
                FROM usuarios
                WHERE email = :email AND estado = TRUE
                LIMIT 1';

        $stmt = $this->conectar()->prepare($sql);
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }
}
