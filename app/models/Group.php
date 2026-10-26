<?php
class Group
{
    public function __construct(private PDO $db) {}

    public function all(int $userId, string $search = '', ?string $tag = null): array
    {
        $sql = 'SELECT g.*,
                       (SELECT COUNT(*) FROM group_members gm WHERE gm.group_id = g.id) AS member_count,
                       (SELECT COUNT(*) FROM group_messages msg WHERE msg.group_id = g.id AND date(msg.created_at)=date("now","localtime")) AS messages_today,
                       (SELECT body FROM group_messages msg WHERE msg.group_id = g.id ORDER BY msg.created_at DESC LIMIT 1) AS last_message,
                       (SELECT created_at FROM group_messages msg WHERE msg.group_id = g.id ORDER BY msg.created_at DESC LIMIT 1) AS last_message_at
                FROM groups g WHERE g.user_id=?';
        $params = [$userId];
        if ($search !== '') {
            $sql .= ' AND (g.name LIKE ? OR g.description LIKE ? OR g.tag LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like);
        }
        if ($tag) {
            $sql .= ' AND g.tag=?';
            $params[] = $tag;
        }
        $sql .= ' ORDER BY COALESCE(last_message_at, g.created_at) DESC';
        $q = $this->db->prepare($sql);
        $q->execute($params);
        return $q->fetchAll();
    }

    public function find(int $id, int $userId): array|false
    {
        $q = $this->db->prepare('SELECT * FROM groups WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
        return $q->fetch();
    }

    public function save(array $v, int $userId, ?int $id = null): void
    {
        $fields = [
            $v['name'],
            $v['description'] ?? null,
            $v['tag'] ?? null,
            $v['color'] ?? '#0D9488',
            $v['icon'] ?? 'users',
        ];
        if ($id) {
            $q = $this->db->prepare(
                'UPDATE groups SET name=?, description=?, tag=?, color=?, icon=? WHERE id=? AND user_id=?'
            );
            $q->execute([...$fields, $id, $userId]);
        } else {
            $q = $this->db->prepare(
                'INSERT INTO groups (user_id, name, description, tag, color, icon) VALUES (?,?,?,?,?,?)'
            );
            $q->execute([$userId, ...$fields]);
        }
    }

    public function delete(int $id, int $userId): void
    {
        $q = $this->db->prepare('DELETE FROM groups WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
    }

    public function count(int $userId): int
    {
        $q = $this->db->prepare('SELECT COUNT(*) FROM groups WHERE user_id=?');
        $q->execute([$userId]);
        return (int) $q->fetchColumn();
    }

    public function messagesToday(int $userId): int
    {
        $q = $this->db->prepare(
            'SELECT COUNT(*) FROM group_messages msg
             JOIN groups g ON g.id = msg.group_id
             WHERE g.user_id=? AND date(msg.created_at)=date("now","localtime")'
        );
        $q->execute([$userId]);
        return (int) $q->fetchColumn();
    }

    public function addMessage(int $groupId, int $userId, string $body): void
    {
        $q = $this->db->prepare('INSERT INTO group_messages (group_id, user_id, body) VALUES (?,?,?)');
        $q->execute([$groupId, $userId, $body]);
    }

    public function messages(int $groupId, int $limit = 100): array
    {
        $q = $this->db->prepare(
            'SELECT msg.*, u.name AS user_name
             FROM group_messages msg
             JOIN users u ON u.id = msg.user_id
             WHERE msg.group_id = ?
             ORDER BY msg.created_at ASC
             LIMIT ?'
        );
        $q->execute([$groupId, $limit]);
        return $q->fetchAll();
    }

    public function lastId(): int
    {
        return (int) $this->db->lastInsertId();
    }
}
