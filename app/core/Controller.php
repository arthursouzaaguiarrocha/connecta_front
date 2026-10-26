<?php
class Controller
{
    protected function view(string $name, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../views/' . $name . '.php';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . BASE_URL . '/index.php?url=' . $path);
        exit;
    }

    protected function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
    }

    protected function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    protected function currentUser(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        static $cached = null;
        if ($cached === null) {
            $cached = (new User(Database::connect()))->find((int) $_SESSION['user_id']);
        }
        return $cached ?: null;
    }

    protected function appView(string $name, array $data = []): void
    {
        $this->requireLogin();
        $data['currentUser'] = $this->currentUser();
        $data['activeMenu']  = $data['activeMenu'] ?? '';
        $data['name']        = $name;
        extract($data);
        // $name is used inside the layout to include the content view
        require __DIR__ . '/../views/layouts/app.php';
    }

    protected function publicView(string $name, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../views/' . $name . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/public.php';
    }
}
