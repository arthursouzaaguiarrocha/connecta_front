<?php
class AuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User(Database::connect());
    }

    public function login(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('contacts');
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_valid($_POST['csrf'] ?? '')) {
                $error = 'Sua sessão expirou. Atualize a página e tente novamente.';
            } else {
                $user = $this->users->byEmail($_POST['email'] ?? '');
                if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int) $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $this->redirect('contacts');
                }
                $error = 'E-mail ou senha inválidos.';
            }
        }
        $this->publicView('auth/login', [
            'title' => 'Login',
            'error' => $error,
            'hideHeader' => true,
        ]);
    }

    public function register(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('contacts');
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $company = trim($_POST['company'] ?? '') ?: null;

            if (!csrf_valid($_POST['csrf'] ?? '')) {
                $error = 'Sua sessão expirou. Tente novamente.';
            } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
                $error = 'Informe nome, e-mail válido e senha com pelo menos 8 caracteres.';
            } elseif ($this->users->byEmail($email)) {
                $error = 'Este e-mail já está cadastrado.';
            } else {
                try {
                    $this->users->create($name, $email, $password, $company, 'Vendedor');
                    $user = $this->users->byEmail($email);
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int) $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $this->redirect('contacts');
                } catch (PDOException $e) {
                    $error = 'Não foi possível concluir o cadastro.';
                }
            }
        }
        $this->publicView('auth/register', [
            'title' => 'Criar Conta',
            'error' => $error,
            'hideHeader' => true,
        ]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        $this->redirect('login');
    }
}
