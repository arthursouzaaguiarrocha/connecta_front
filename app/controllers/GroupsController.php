<?php
class GroupsController extends Controller
{
    private Group $groups;

    public function __construct()
    {
        $this->groups = new Group(Database::connect());
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $tag = $_GET['tag'] ?? null;
        $userId = (int) $_SESSION['user_id'];
        $list = $this->groups->all($userId, $search, $tag ?: null);
        $this->appView('groups/index', [
            'title' => 'Grupos',
            'activeMenu' => 'groups',
            'groups' => $list,
            'search' => $search,
            'tag' => $tag,
            'totalGroups' => $this->groups->count($userId),
            'messagesToday' => $this->groups->messagesToday($userId),
            'pendingRequests' => $this->groups->pendingRequestsForAdmin($userId),
            'showSearch' => true,
        ]);
    }

    public function form(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $error = '';
        $success = '';
        $group = ['name' => '', 'description' => '', 'tag' => '', 'color' => '#0D9488', 'icon' => 'users', 'invite_code' => null];
        $members = [];
        $requests = [];

        if ($id) {
            $found = $this->groups->findOwned($id, (int) $_SESSION['user_id']);
            if (!$found) $this->redirect('groups');
            $group = $found;
            $members = $this->groups->userMembers($id);
            $requests = $this->groups->pendingRequests($id);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_valid($_POST['csrf'] ?? '')) {
                $error = 'Sessão expirada.';
            } else {
                $action = $_POST['action'] ?? 'save';

                if ($action === 'regenerate_code' && $id) {
                    $newCode = $this->groups->regenerateInviteCode($id, (int) $_SESSION['user_id']);
                    if ($newCode) {
                        $group['invite_code'] = $newCode;
                        $success = 'Novo código gerado: ' . $newCode;
                    }
                } elseif ($action === 'respond_request') {
                    $reqId = (int) ($_POST['request_id'] ?? 0);
                    $resp = $_POST['response'] ?? '';
                    if ($this->groups->respondJoinRequest($reqId, (int) $_SESSION['user_id'], $resp)) {
                        $success = $resp === 'accept' ? 'Usuário aceito no grupo!' : 'Pedido recusado.';
                        $requests = $this->groups->pendingRequests($id);
                        $members = $this->groups->userMembers($id);
                    } else {
                        $error = 'Não foi possível processar o pedido.';
                    }
                } else {
                    $v = [
                        'name' => trim($_POST['name'] ?? ''),
                        'description' => trim($_POST['description'] ?? ''),
                        'tag' => trim($_POST['tag'] ?? ''),
                        'color' => $_POST['color'] ?? '#0D9488',
                        'icon' => $_POST['icon'] ?? 'users',
                    ];
                    if ($v['name'] === '') {
                        $error = 'Nome é obrigatório.';
                        $group = array_merge($group, $v);
                    } else {
                        $newId = $this->groups->save($v, (int) $_SESSION['user_id'], $id);
                        if (!$id) {
                            // Redireciona para a edição do grupo recém-criado para mostrar o código
                            header('Location: ' . BASE_URL . '/index.php?url=groups/form&id=' . $newId);
                            exit;
                        }
                        $this->redirect('groups');
                    }
                }
            }
        }

        $this->appView('groups/form', [
            'title' => $id ? 'Editar grupo' : 'Novo grupo',
            'activeMenu' => 'groups',
            'group' => $group,
            'id' => $id,
            'error' => $error,
            'success' => $success,
            'members' => $members,
            'requests' => $requests,
        ]);
    }

    public function delete(): void
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid($_POST['csrf'] ?? '')) {
            $this->groups->delete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);
        }
        $this->redirect('groups');
    }

    /** Página para entrar em um grupo com código */
    public function join(): void
    {
        $this->requireLogin();
        $error = '';
        $success = '';
        $preview = null;
        $userId = (int) $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_valid($_POST['csrf'] ?? '')) {
                $error = 'Sessão expirada.';
            } else {
                $code = strtoupper(trim($_POST['code'] ?? ''));
                $message = trim($_POST['message'] ?? '');

                if ($code === '') {
                    $error = 'Informe o código de convite.';
                } else {
                    $group = $this->groups->findByInviteCode($code);
                    if (!$group) {
                        $error = 'Código inválido ou grupo não encontrado.';
                    } elseif ($this->groups->isMember((int)$group['id'], $userId)) {
                        $error = 'Você já faz parte deste grupo.';
                        $preview = $group;
                    } else {
                        $ok = $this->groups->createJoinRequest((int)$group['id'], $userId, $message ?: null);
                        if ($ok) {
                            $user = $this->currentUser();
                            $this->groups->notifyAdminsOfJoinRequest(
                                (int)$group['id'],
                                $userId,
                                $user['name'] ?? 'Usuário'
                            );
                            $success = 'Pedido enviado! Aguarde a aprovação do administrador do grupo "' . $group['name'] . '".';
                            $preview = $group;
                        } else {
                            $error = 'Você já tem um pedido pendente para este grupo.';
                            $preview = $group;
                        }
                    }
                }
            }
        } elseif (!empty($_GET['code'])) {
            // Pré-visualização via link
            $preview = $this->groups->findByInviteCode($_GET['code']);
        }

        $this->appView('groups/join', [
            'title' => 'Entrar em um grupo',
            'activeMenu' => 'groups',
            'error' => $error,
            'success' => $success,
            'preview' => $preview,
        ]);
    }
}
