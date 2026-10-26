<?php
class Event
{
    public function __construct(private PDO $db) {}

    public function byDate(int $userId, string $date): array
    {
        $q = $this->db->prepare(
            'SELECT * FROM events WHERE user_id=? AND event_date=? ORDER BY start_time'
        );
        $q->execute([$userId, $date]);
        return $q->fetchAll();
    }

    public function byMonth(int $userId, int $year, int $month): array
    {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-t', strtotime($start));
        $q = $this->db->prepare(
            'SELECT * FROM events WHERE user_id=? AND event_date BETWEEN ? AND ? ORDER BY event_date, start_time'
        );
        $q->execute([$userId, $start, $end]);
        return $q->fetchAll();
    }

    public function find(int $id, int $userId): array|false
    {
        $q = $this->db->prepare('SELECT * FROM events WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
        return $q->fetch();
    }

    public function save(array $v, int $userId, ?int $id = null): void
    {
        $fields = [
            $v['title'],
            $v['description'] ?? null,
            $v['event_date'],
            $v['start_time'],
            $v['end_time'] ?? null,
            $v['location'] ?? null,
            $v['category'] ?? 'Trabalho',
            $v['color'] ?? null,
        ];
        if ($id) {
            $q = $this->db->prepare(
                'UPDATE events SET title=?, description=?, event_date=?, start_time=?, end_time=?, location=?, category=?, color=? WHERE id=? AND user_id=?'
            );
            $q->execute([...$fields, $id, $userId]);
        } else {
            $q = $this->db->prepare(
                'INSERT INTO events (user_id, title, description, event_date, start_time, end_time, location, category, color) VALUES (?,?,?,?,?,?,?,?,?)'
            );
            $q->execute([$userId, ...$fields]);
        }
    }

    public function delete(int $id, int $userId): void
    {
        $q = $this->db->prepare('DELETE FROM events WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
    }

    public function countToday(int $userId): int
    {
        $q = $this->db->prepare('SELECT COUNT(*) FROM events WHERE user_id=? AND event_date=date("now","localtime")');
        $q->execute([$userId]);
        return (int) $q->fetchColumn();
    }
}
