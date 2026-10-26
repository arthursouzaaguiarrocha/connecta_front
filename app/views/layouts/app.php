<?php
$base = BASE_URL;
$user = $currentUser ?? null;
$theme = $user['theme'] ?? 'light';
$active = $activeMenu ?? '';
$pageTitle = htmlspecialchars($title ?? 'Connecta', ENT_QUOTES, 'UTF-8');
$initials = $user ? mb_strtoupper(mb_substr($user['name'], 0, 1)) : '?';
?>
<!doctype html>
<html lang="pt-BR" data-theme="<?= e($theme) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $pageTitle ?> · Connecta</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/app.css">
</head>
<body>
<div class="app-shell">
  <div class="sidebar-overlay" id="sidebar-overlay"></div>

  <aside class="sidebar <?= $theme === 'dark' ? 'dark' : '' ?>" id="sidebar">
    <a class="sidebar-brand" href="<?= $base ?>/index.php?url=contacts">
      <img src="<?= $base ?>/assets/logo_connecta.png" alt="">
      <span>
        <b>connecta</b>
        <small>AGENDA DE CONTATOS</small>
      </span>
    </a>

    <nav class="sidebar-nav">
      <a href="<?= $base ?>/index.php?url=contacts" class="<?= $active === 'contacts' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="bi bi-people"></i></span> Contatos
      </a>
      <a href="<?= $base ?>/index.php?url=agenda" class="<?= $active === 'agenda' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="bi bi-calendar3"></i></span> Agenda
      </a>
      <a href="<?= $base ?>/index.php?url=tasks" class="<?= $active === 'tasks' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="bi bi-check2-square"></i></span> Tarefas
      </a>
      <a href="<?= $base ?>/index.php?url=groups" class="<?= $active === 'groups' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="bi bi-collection"></i></span> Grupos
      </a>
      <a href="<?= $base ?>/index.php?url=chat" class="<?= $active === 'chat' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="bi bi-chat-dots"></i></span> Chat
      </a>
      <a href="<?= $base ?>/index.php?url=settings" class="<?= $active === 'settings' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="bi bi-gear"></i></span> Config
      </a>
      <a href="<?= $base ?>/index.php?url=logout" class="logout">
        <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span> Sair
      </a>
    </nav>

    <?php if ($user): ?>
    <div class="sidebar-user">
      <div class="avatar"><?= e($initials) ?></div>
      <div class="info">
        <b><?= e($user['name']) ?></b>
        <small><?= e($user['company'] ?? '') ?> · <?= e($user['role'] ?? 'Usuário') ?></small>
      </div>
    </div>
    <?php endif; ?>
  </aside>

  <div class="main-wrap">
    <header class="topbar">
      <div class="topbar-left">
        <button class="topbar-btn menu-toggle" id="menu-toggle" aria-label="Menu"><i class="bi bi-list"></i></button>
        <?php if (!empty($showSearch)): ?>
        <form class="topbar-search" method="get" action="<?= $base ?>/index.php">
          <input type="hidden" name="url" value="<?= e($active) ?>">
          <span class="icon"><i class="bi bi-search"></i></span>
          <input type="search" name="q" value="<?= e($search ?? '') ?>" placeholder="Pesquisar...">
        </form>
        <?php endif; ?>
      </div>
      <div class="topbar-right">
        <button class="topbar-btn" title="Notificações"><i class="bi bi-bell"></i></button>
        <button class="topbar-btn" id="theme-toggle" title="Alternar tema"><i class="bi bi-moon-stars"></i></button>
      </div>
    </header>

    <main class="content">
      <?php require __DIR__ . '/../' . $name . '.php'; ?>
    </main>

    <footer class="app-footer">Desenvolvido pela Connecta</footer>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= $base ?>/assets/app.js"></script>
</body>
</html>
