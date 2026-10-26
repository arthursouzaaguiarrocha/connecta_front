<?php $base = BASE_URL; ?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Connecta', ENT_QUOTES, 'UTF-8') ?> | Connecta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../">
</head>

<body>
    <header class="topbar"><a class="brand" href="<?= $base ?>/index.php"><img src="<?= $base ?>/assets/logo_connecta.png" alt=""><span><b>Connecta</b><small>Agenda de Contatos</small></span></a>
        <nav><a href="<?= $base ?>/index.php#sobre">Sobre</a><?php if (!empty($_SESSION['user_id'])): ?><a href="<?= $base ?>/index.php?url=contacts">Minha agenda</a><span class="welcome">Olá, <?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span><a class="btn btn-outline" href="<?= $base ?>/index.php?url=logout">Sair</a><?php else: ?><a href="<?= $base ?>/index.php?url=login">Entrar</a><a class="btn btn-blue" href="<?= $base ?>/index.php?url=register">Criar conta</a><?php endif; ?></nav><button class="mobile-menu" aria-label="Abrir menu">☰</button>
    </header>
    <main class="container">