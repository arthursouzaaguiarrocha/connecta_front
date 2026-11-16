<?php
class Group
{
    public function __construct(private PDO $db) {}

    /** Gera um código de convite único (ex: ABC123XY) */
    public function generateInviteCode(): string
    {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $q = $this->db->prepare('SELECT 1 FROM groups WHERE invite_code = ?');
            $q->execute([$code]);
            $exists = $q->fetchColumn();
        } while ($exists);
        return $code;
    }

    /** Lista grupos onde o usuário é dono OU membro */
    public function all(int $userId, string $search = '', ?string $tag = null): array
    {
        $sql = 'SELECT g.*,
                       (SELECT COUNT(*) FROM group_user_members gum WHERE gum.group_id = g.id) AS member_count,
                       (SELECT COUNT(*) FROM group_messages msg WHERE msg.group_id = g.id AND date(msg.created_at)=date("now","localtime")) AS messages_today,
                       (SELECT body FROM group_messages msg WHERE msg.group_id = g.id ORDER BY msg.created_at DESC LIMIT 1) AS last_message,
                       (SELECT created_at FROM group_messages msg WHERE msg.group_id = g.id ORDER BY msg.created_at DESC LIMIT 1) AS last_message_at,
                       CASE WHEN g.user_id = ? THEN "admin" ELSE COALESCE(
                           (SELECT role FROM group_user_members gum2 WHERE gum2.group_id = g.id AND gum2.user_id = ?), "member"
                       ) END AS my_role
                FROM groups g
                WHERE g.user_id = ?
                   OR g.id IN (SELECT group_id FROM group_user_members WHERE user_id = ?)';
        $params = [$userId, $userId, $userId, $userId];

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

    /** Busca grupo se o usuário for dono ou membro */
    public function find(int $id, int $userId): array|false
    {
        $q = $this->db->prepare(
            'SELECT g.*,
                    CASE WHEN g.user_id = ? THEN "admin" ELSE COALESCE(
                        (SELECT role FROM group_user_members WHERE group_id = g.id AND user_id = ?), NULL
                    ) END AS my_role
             FROM groups g
             WHERE g.id = ?
               AND (g.user_id = ? OR g.id IN (SELECT group_id FROM group_user_members WHERE user_id = ?))'
        );
        $q->execute([$userId, $userId, $id, $userId, $userId]);
        return $q->fetch();
    }

    /** Busca grupo apenas pelo dono (para edição) */
    public function findOwned(int $id, int $userId): array|false
    {
        $q = $this->db->prepare('SELECT * FROM groups WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
        return $q->fetch();
    }

    /** Busca grupo pelo código de convite */
    public function findByInviteCode(string $code): array|false
    {
        $q = $this->db->prepare('SELECT * FROM groups WHERE invite_code = ?');
        $q->execute([strtoupper(trim($code))]);
        return $q->fetch();
    }

    public function save(array $v, int $userId, ?int $id = null): int
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
            return $id;
        }

        $code = $this->generateInviteCode();
        $q = $this->db->prepare(
            'INSERT INTO groups (user_id, name, description, tag, color, icon, invite_code) VALUES (?,?,?,?,?,?,?)'
        );
        $q->execute([$userId, ...$fields, $code]);
        $groupId = (int) $this->db->lastInsertId();

        // Dono também entra como membro admin
        $this->addUserMember($groupId, $userId, 'admin');

        return $groupId;
    }

    public function delete(int $id, int $userId): void
    {
        $q = $this->db->prepare('DELETE FROM groups WHERE id=? AND user_id=?');
        $q->execute([$id, $userId]);
    }

    public function count(int $userId): int
    {
        $q = $this->db->prepare(
            'SELECT COUNT(*) FROM groups g
             WHERE g.user_id=? OR g.id IN (SELECT group_id FROM group_user_members WHERE user_id=?)'
        );
        $q->execute([$userId, $userId]);
        return (int) $q->fetchColumn();
    }

    public function messagesToday(int $userId): int
    {
        $q = $this->db->prepare(
            'SELECT COUNT(*) FROM group_messages msg
             JOIN groups g ON g.id = msg.group_id
             WHERE (g.user_id=? OR g.id IN (SELECT group_id FROM group_user_members WHERE user_id=?))
               AND date(msg.created_at)=date("now","localtime")'
        );
        $q->execute([$userId, $userId]);
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

    // ─── Membros usuários ───────────────────────────────────────────

    public function addUserMember(int $groupId, int $userId, string $role = 'member'): void
    {
        $q = $this->db->prepare(
            'INSERT OR IGNORE INTO group_user_members (group_id, user_id, role) VALUES (?,?,?)'
        );
        $q->execute([$groupId, $userId, $role]);
    }

    public function removeUserMember(int $groupId, int $userId): void
    {
        $q = $this->db->prepare('DELETE FROM group_user_members WHERE group_id=? AND user_id=?');
        $q->execute([$groupId, $userId]);
    }

    public function isMember(int $groupId, int $userId): bool
    {
        $q = $this->db->prepare(
            'SELECT 1 FROM groups WHERE id=? AND user_id=?
             UNION
             SELECT 1 FROM group_user_members WHERE group_id=? AND user_id=?'
        );
        $q->execute([$groupId, $userId, $groupId, $userId]);
        return (bool) $q->fetchColumn();
    }

    public function isAdmin(int $groupId, int $userId): bool
    {
        $q = $this->db->prepare(
            'SELECT 1 FROM groups WHERE id=? AND user_id=?
             UNION
             SELECT 1 FROM group_user_members WHERE group_id=? AND user_id=? AND role="admin"'
        );
        $q->execute([$groupId, $userId, $groupId, $userId]);
        return (bool) $q->fetchColumn();
    }

    public function userMembers(int $groupId): array
    {
        $q = $this->db->prepare(
            'SELECT u.id, u.name, u.email, gum.role, gum.joined_at
             FROM group_user_members gum
             JOIN users u ON u.id = gum.user_id
             WHERE gum.group_id = ?
             ORDER BY CASE gum.role WHEN "admin" THEN 0 ELSE 1 END, u.name'
        );
        $q->execute([$groupId]);
        return $q->fetchAll();
    }

    // ─── Pedidos de entrada ─────────────────────────────────────────

    public function createJoinRequest(int $groupId, int $userId, ?string $message = null): bool
    {
        if ($this->isMember($groupId, $userId)) {
            return false;
        }
        $q = $this->db->prepare(
            'SELECT status FROM group_join_requests WHERE group_id=? AND user_id=?'
        );
        $q->execute([$groupId, $userId]);
        $existing = $q->fetchColumn();
        if ($existing === 'pending') {
            return false;
        }

        $this->db->prepare('DELETE FROM group_join_requests WHERE group_id=? AND user_id=?')
            ->execute([$groupId, $userId]);

        $q = $this->db->prepare(
            'INSERT INTO group_join_requests (group_id, user_id, message, status) VALUES (?,?,?,"pending")'
        );
        $q->execute([$groupId, $userId, $message]);
        return true;
    }

    public function pendingRequests(int $groupId): array
    {
        $q = $this->db->prepare(
            'SELECT r.*, u.name AS user_name, u.email AS user_email
             FROM group_join_requests r
             JOIN users u ON u.id = r.user_id
             WHERE r.group_id = ? AND r.status = "pending"
             ORDER BY r.created_at ASC'
        );
        $q->execute([$groupId]);
        return $q->fetchAll();
    }

    public function pendingRequestsForAdmin(int $adminUserId): array
    {
        $q = $this->db->prepare(
            'SELECT r.*, u.name AS user_name, u.email AS user_email, g.name AS group_name, g.id AS group_id
             FROM group_join_requests r
             JOIN users u ON u.id = r.user_id
             JOIN groups g ON g.id = r.group_id
             WHERE g.user_id = ? AND r.status = "pending"
             ORDER BY r.created_at DESC'
        );
        $q->execute([$adminUserId]);
        return $q->fetchAll();
    }

    public function respondJoinRequest(int $requestId, int $adminUserId, string $action): bool
    {
        $q = $this->db->prepare(
            'SELECT r.*, g.user_id AS owner_id, g.name AS group_name
             FROM group_join_requests r
             JOIN groups g ON g.id = r.group_id
             WHERE r.id = ? AND r.status = "pending"'
        );
        $q->execute([$requestId]);
        $req = $q->fetch();
        if (!$req || (int)$req['owner_id'] !== $adminUserId) {
            return false;
        }

        $status = $action === 'accept' ? 'accepted' : 'rejected';
        $this->db->prepare(
            'UPDATE group_join_requests SET status=?, responded_at=CURRENT_TIMESTAMP WHERE id=?'
        )->execute([$status, $requestId]);

        if ($status === 'accepted') {
            $this->addUserMember((int)$req['group_id'], (int)$req['user_id'], 'member');
        }

        $title = $status === 'accepted' ? 'Pedido aceito!' : 'Pedido recusado';
        $body = $status === 'accepted'
            ? 'Você foi aceito no grupo "' . $req['group_name'] . '".'
            : 'Seu pedido para entrar no grupo "' . $req['group_name'] . '" foi recusado.';

        $this->createNotification(
            (int)$req['user_id'],
            $status === 'accepted' ? 'join_accepted' : 'join_rejected',
            $title,
            $body,
            json_encode(['group_id' => (int)$req['group_id']])
        );

        return true;
    }

    // ─── Notificações ───────────────────────────────────────────────

    public function createNotification(int $userId, string $type, string $title, ?string $body = null, ?string $data = null): void
    {
        $q = $this->db->prepare(
            'INSERT INTO notifications (user_id, type, title, body, data) VALUES (?,?,?,?,?)'
        );
        $q->execute([$userId, $type, $title, $body, $data]);
    }

    public function notifyAdminsOfJoinRequest(int $groupId, int $requesterId, string $requesterName): void
    {
        $q = $this->db->prepare('SELECT user_id, name FROM groups WHERE id=?');
        $q->execute([$groupId]);
        $group = $q->fetch();
        if (!$group) return;

        $this->createNotification(
            (int)$group['user_id'],
            'join_request',
            'Novo pedido de entrada',
            $requesterName . ' quer entrar no grupo "' . $group['name'] . '".',
            json_encode(['group_id' => $groupId, 'user_id' => $requesterId])
        );
    }

    public function unreadNotifications(int $userId, int $limit = 20): array
    {
        $q = $this->db->prepare(
            'SELECT * FROM notifications WHERE user_id=? AND is_read=0 ORDER BY created_at DESC LIMIT ?'
        );
        $q->execute([$userId, $limit]);
        return $q->fetchAll();
    }

    public function unreadCount(int $userId): int
    {
        $q = $this->db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0');
        $q->execute([$userId]);
        return (int) $q->fetchColumn();
    }

    public function markNotificationRead(int $id, int $userId): void
    {
        $this->db->prepare('UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?')
            ->execute([$id, $userId]);
    }

    public function markAllNotificationsRead(int $userId): void
    {
        $this->db->prepare('UPDATE notifications SET is_read=1 WHERE user_id=? AND is_read=0')
            ->execute([$userId]);
    }

    /** Regenera o código de convite (apenas dono) */
    public function regenerateInviteCode(int $groupId, int $userId): ?string
    {
        $group = $this->findOwned($groupId, $userId);
        if (!$group) return null;
        $code = $this->generateInviteCode();
        $this->db->prepare('UPDATE groups SET invite_code=? WHERE id=? AND user_id=?')
            ->execute([$code, $groupId, $userId]);
        return $code;
    }
}
