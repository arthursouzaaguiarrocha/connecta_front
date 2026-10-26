<?php
class User
{
    public function __construct(private PDO $db) {}

    public function create(string $name, string $email, string $password, ?string $company = null, ?string $role = null): bool
    {
        $q = $this->db->prepare(
            'INSERT INTO users (name, email, password, company, role) VALUES (?,?,?,?,?)'
        );
        return $q->execute([
            $name,
            mb_strtolower(trim($email)),
            password_hash($password, PASSWORD_DEFAULT),
            $company,
            $role ?? 'Usuário',
        ]);
    }

    public function byEmail(string $email): array|false
    {
        $q = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $q->execute([mb_strtolower(trim($email))]);
        return $q->fetch();
    }

    public function find(int $id): array|false
    {
        $q = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $q->execute([$id]);
        return $q->fetch();
    }

    public function updateProfile(int $id, array $data): bool
    {
        $q = $this->db->prepare(
            'UPDATE users SET name=?, company=?, role=?, avatar=? WHERE id=?'
        );
        return $q->execute([
            $data['name'],
            $data['company'] ?? null,
            $data['role'] ?? 'Usuário',
            $data['avatar'] ?? null,
            $id,
        ]);
    }

    public function updatePassword(int $id, string $password): bool
    {
        $q = $this->db->prepare('UPDATE users SET password=? WHERE id=?');
        return $q->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function updateSettings(int $id, array $data): bool
    {
        $q = $this->db->prepare(
            'UPDATE users SET theme=?, notify_device=?, notify_email=?, profile_visible=? WHERE id=?'
        );
        return $q->execute([
            $data['theme'] ?? 'light',
            !empty($data['notify_device']) ? 1 : 0,
            !empty($data['notify_email']) ? 1 : 0,
            !empty($data['profile_visible']) ? 1 : 0,
            $id,
        ]);
    }
}
