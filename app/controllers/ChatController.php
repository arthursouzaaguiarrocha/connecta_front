<?php
class ChatController extends Controller
{
    private Group $groups;

    public function __construct()
    {
        $this->groups = new Group(Database::connect());
    }

    public function index(): void
    {
        $userId = (int) $_SESSION['user_id'];
        $groupId = isset($_GET['group']) ? (int) $_GET['group'] : null;
        $allGroups = $this->groups->all($userId);

        $activeGroup = null;
        $messages = [];

        if ($groupId) {
            $activeGroup = $this->groups->find($groupId, $userId);
            if ($activeGroup) {
                $messages = $this->groups->messages($groupId);
            } else {
                $groupId = null;
            }
        }

        if (!$activeGroup && $allGroups) {
            $activeGroup = $allGroups[0];
            $groupId = (int) $activeGroup['id'];
            $messages = $this->groups->messages($groupId);
        }

        $this->appView('chat/index', [
            'title' => 'Chat',
            'activeMenu' => 'chat',
            'groups' => $allGroups,
            'activeGroup' => $activeGroup,
            'groupId' => $groupId,
            'messages' => $messages,
            'currentUserId' => $userId,
        ]);
    }

    public function send(): void
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid($_POST['csrf'] ?? '')) {
            $this->redirect('chat');
        }

        $groupId = (int) ($_POST['group_id'] ?? 0);
        $body = trim($_POST['body'] ?? '');
        $userId = (int) $_SESSION['user_id'];

        $group = $this->groups->find($groupId, $userId);
        if ($group && $body !== '') {
            $this->groups->addMessage($groupId, $userId, $body);
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'body' => $body, 'time' => date('H:i')]);
            exit;
        }

        header('Location: ' . BASE_URL . '/index.php?url=chat&group=' . $groupId);
        exit;
    }
}
