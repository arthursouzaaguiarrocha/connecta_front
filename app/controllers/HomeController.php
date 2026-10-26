<?php
class HomeController extends Controller
{
    public function index(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('contacts');
        }
        $this->publicView('home', ['title' => 'Seu tempo é valioso']);
    }
}
