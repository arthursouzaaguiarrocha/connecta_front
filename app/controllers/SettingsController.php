<?php
class SettingsController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User(Database::connect());
    }

    public function index(): void
    {
        $user = $this->currentUser();
        $this->appView('settings/index', [
            'title' => 'Configurações',
            'activeMenu' => 'settings',
            'user' => $user,
        ]);
    }

    public function save(): void
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid($_POST['csrf'] ?? '')) {
            $this->redirect('settings');
        }

        $section = $_POST['section'] ?? '';
        $uid = (int) $_SESSION['user_id'];

        if ($section === 'profile') {
            $this->users->updateProfile($uid, [
                'name' => trim($_POST['name'] ?? ''),
                'company' => trim($_POST['company'] ?? '') ?: null,
                'role' => trim($_POST['role'] ?? '') ?: 'Usuário',
            ]);
            $_SESSION['user_name'] = trim($_POST['name'] ?? '');
        } elseif ($section === 'password') {
            $user = $this->users->find($uid);
            if ($user && password_verify($_POST['current_password'] ?? '', $user['password'])) {
                if (strlen($_POST['new_password'] ?? '') >= 8) {
                    $this->users->updatePassword($uid, $_POST['new_password']);
                }
            }
        } elseif ($section === 'notifications' || $section === 'privacy' || $section === 'theme') {
            $this->users->updateSettings($uid, [
                'theme' => $_POST['theme'] ?? ($this->currentUser()['theme'] ?? 'light'),
                'notify_device' => isset($_POST['notify_device']),
                'notify_email' => isset($_POST['notify_email']),
                'profile_visible' => isset($_POST['profile_visible']),
            ]);
        }

        $this->redirect('settings');
    }
}
