<?php
class Contact
{
    public function __construct(private PDO $db) {}

    public function all(int $userId, string $search = ''): array
    {
        $sql = 'SELECT * FROM contacts WHERE user_id = ?';
        $params = [$userId];
        if ($search !== '') {
            $sql .= ' AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR whatsapp LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like, $like);
        }
        $sql .= ' ORDER BY name COLLATE NOCASE';
        $q = $this->db->prepare($sql);
        $q->execute($params);
        return $q->fetchAll();
    }

    public function find(int $id, int $userId): array|false
    {
        $q = $this->db->prepare('SELECT * FROM contacts WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
        return $q->fetch();
    }

    public function save(array $v, int $userId, ?int $id = null): void
    {
        $fields = [
            $v['name'],
            $v['phone'],
            $v['email'] ?: null,
            $v['whatsapp'] ?: null,
            $v['telegram'] ?? null,
            $v['teams'] ?? null,
            $v['address'] ?: null,
            $v['notes'] ?: null,
            $v['status'] ?? 'Ativo',
        ];

        if ($id) {
            $q = $this->db->prepare(
                'UPDATE contacts SET name=?, phone=?, email=?, whatsapp=?, telegram=?, teams=?, address=?, notes=?, status=?, updated_at=CURRENT_TIMESTAMP WHERE id=? AND user_id=?'
            );
            $q->execute([...$fields, $id, $userId]);
        } else {
            $q = $this->db->prepare(
                'INSERT INTO contacts (user_id, name, phone, email, whatsapp, telegram, teams, address, notes, status) VALUES (?,?,?,?,?,?,?,?,?,?)'
            );
            $q->execute([$userId, ...$fields]);
        }
    }

    public function delete(int $id, int $userId): void
    {
        $q = $this->db->prepare('DELETE FROM contacts WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
    }

    public function count(int $userId): int
    {
        $q = $this->db->prepare('SELECT COUNT(*) FROM contacts WHERE user_id=?');
        $q->execute([$userId]);
        return (int) $q->fetchColumn();
    }
}
