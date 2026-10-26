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
        $list = $this->groups->all((int) $_SESSION['user_id'], $search, $tag ?: null);
        $this->appView('groups/index', [
            'title' => 'Grupos',
            'activeMenu' => 'groups',
            'groups' => $list,
            'search' => $search,
            'tag' => $tag,
            'totalGroups' => $this->groups->count((int) $_SESSION['user_id']),
            'messagesToday' => $this->groups->messagesToday((int) $_SESSION['user_id']),
            'showSearch' => true,
        ]);
    }

    public function form(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $error = '';
        $group = ['name' => '', 'description' => '', 'tag' => '', 'color' => '#0D9488', 'icon' => 'users'];

        if ($id) {
            $found = $this->groups->find($id, (int) $_SESSION['user_id']);
            if (!$found) $this->redirect('groups');
            $group = $found;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_valid($_POST['csrf'] ?? '')) {
                $error = 'Sessão expirada.';
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
                    $group = $v;
                } else {
                    $this->groups->save($v, (int) $_SESSION['user_id'], $id);
                    $this->redirect('groups');
                }
            }
        }

        $this->appView('groups/form', [
            'title' => $id ? 'Editar grupo' : 'Novo grupo',
            'activeMenu' => 'groups',
            'group' => $group,
            'id' => $id,
            'error' => $error,
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
}
