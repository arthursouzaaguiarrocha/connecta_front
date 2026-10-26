<?php
class ContactsController extends Controller
{
    private Contact $contacts;

    public function __construct()
    {
        $this->contacts = new Contact(Database::connect());
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $list = $this->contacts->all((int) $_SESSION['user_id'], $search);
        $this->appView('contacts/index', [
            'title' => 'Contatos',
            'activeMenu' => 'contacts',
            'contacts' => $list,
            'search' => $search,
            'showSearch' => true,
        ]);
    }

    public function form(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $error = '';
        $contact = [
            'name' => '', 'phone' => '', 'email' => '', 'whatsapp' => '',
            'telegram' => '', 'teams' => '', 'address' => '', 'notes' => '', 'status' => 'Ativo',
        ];

        if ($id) {
            $found = $this->contacts->find($id, (int) $_SESSION['user_id']);
            if (!$found) {
                $this->redirect('contacts');
            }
            $contact = $found;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_valid($_POST['csrf'] ?? '')) {
                $error = 'Sessão expirada.';
            } else {
                $v = [
                    'name' => trim($_POST['name'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'whatsapp' => trim($_POST['whatsapp'] ?? ''),
                    'telegram' => trim($_POST['telegram'] ?? ''),
                    'teams' => trim($_POST['teams'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'notes' => trim($_POST['notes'] ?? ''),
                    'status' => $_POST['status'] ?? 'Ativo',
                ];
                if ($v['name'] === '' || $v['phone'] === '') {
                    $error = 'Nome e telefone são obrigatórios.';
                    $contact = $v;
                } else {
                    $this->contacts->save($v, (int) $_SESSION['user_id'], $id);
                    $this->redirect('contacts');
                }
            }
        }

        $this->appView('contacts/form', [
            'title' => $id ? 'Editar contato' : 'Novo contato',
            'activeMenu' => 'contacts',
            'contact' => $contact,
            'id' => $id,
            'error' => $error,
        ]);
    }

    public function delete(): void
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid($_POST['csrf'] ?? '')) {
            $this->contacts->delete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);
        }
        $this->redirect('contacts');
    }
}
