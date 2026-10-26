<?php
class TasksController extends Controller
{
    private Task $tasks;

    public function __construct()
    {
        $this->tasks = new Task(Database::connect());
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $list = $this->tasks->all((int) $_SESSION['user_id'], $search);
        $this->appView('tasks/index', [
            'title' => 'Tarefas',
            'activeMenu' => 'tasks',
            'tasks' => $list,
            'search' => $search,
            'showSearch' => true,
        ]);
    }

    public function form(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $error = '';
        $task = [
            'title' => '', 'description' => '', 'priority' => 'media',
            'status' => 'pendente', 'due_date' => '', 'due_time' => '',
        ];

        if ($id) {
            $found = $this->tasks->find($id, (int) $_SESSION['user_id']);
            if (!$found) $this->redirect('tasks');
            $task = $found;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_valid($_POST['csrf'] ?? '')) {
                $error = 'Sessão expirada.';
            } else {
                $v = [
                    'title' => trim($_POST['title'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                    'priority' => $_POST['priority'] ?? 'media',
                    'status' => $_POST['status'] ?? 'pendente',
                    'due_date' => $_POST['due_date'] ?: null,
                    'due_time' => $_POST['due_time'] ?: null,
                ];
                if ($v['title'] === '') {
                    $error = 'Título é obrigatório.';
                    $task = $v;
                } else {
                    $this->tasks->save($v, (int) $_SESSION['user_id'], $id);
                    $this->redirect('tasks');
                }
            }
        }

        $this->appView('tasks/form', [
            'title' => $id ? 'Editar tarefa' : 'Nova tarefa',
            'activeMenu' => 'tasks',
            'task' => $task,
            'id' => $id,
            'error' => $error,
        ]);
    }

    public function delete(): void
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid($_POST['csrf'] ?? '')) {
            $this->tasks->delete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);
        }
        $this->redirect('tasks');
    }

    public function toggle(): void
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid($_POST['csrf'] ?? '')) {
            $this->tasks->toggleComplete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);
        }
        $this->redirect('tasks');
    }
}
