<?php
class Task
{
    public function __construct(private PDO $db) {}

    public function all(int $userId, string $search = '', ?string $status = null): array
    {
        $sql = 'SELECT * FROM tasks WHERE user_id=?';
        $params = [$userId];
        if ($search !== '') {
            $sql .= ' AND (title LIKE ? OR description LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like);
        }
        if ($status) {
            $sql .= ' AND status=?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY CASE priority WHEN "alta" THEN 1 WHEN "media" THEN 2 WHEN "baixa" THEN 3 ELSE 4 END, due_date, due_time';
        $q = $this->db->prepare($sql);
        $q->execute($params);
        return $q->fetchAll();
    }

    public function find(int $id, int $userId): array|false
    {
        $q = $this->db->prepare('SELECT * FROM tasks WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
        return $q->fetch();
    }

    public function save(array $v, int $userId, ?int $id = null): void
    {
        $fields = [
            $v['title'],
            $v['description'] ?? null,
            $v['priority'] ?? 'media',
            $v['status'] ?? 'pendente',
            $v['due_date'] ?? null,
            $v['due_time'] ?? null,
        ];
        if ($id) {
            $completed = ($v['status'] ?? '') === 'concluida' ? date('Y-m-d H:i:s') : null;
            $q = $this->db->prepare(
                'UPDATE tasks SET title=?, description=?, priority=?, status=?, due_date=?, due_time=?, completed_at=? WHERE id=? AND user_id=?'
            );
            $q->execute([...$fields, $completed, $id, $userId]);
        } else {
            $q = $this->db->prepare(
                'INSERT INTO tasks (user_id, title, description, priority, status, due_date, due_time) VALUES (?,?,?,?,?,?,?)'
            );
            $q->execute([$userId, ...$fields]);
        }
    }

    public function delete(int $id, int $userId): void
    {
        $q = $this->db->prepare('DELETE FROM tasks WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
    }

    public function toggleComplete(int $id, int $userId): void
    {
        $task = $this->find($id, $userId);
        if (!$task) return;
        $newStatus = $task['status'] === 'concluida' ? 'pendente' : 'concluida';
        $completed = $newStatus === 'concluida' ? date('Y-m-d H:i:s') : null;
        $q = $this->db->prepare('UPDATE tasks SET status=?, completed_at=? WHERE id=? AND user_id=?');
        $q->execute([$newStatus, $completed, $id, $userId]);
    }
}
