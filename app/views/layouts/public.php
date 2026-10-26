<?php
$base = BASE_URL;
$pageTitle = htmlspecialchars($title ?? 'Connecta', ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $pageTitle ?> · Connecta</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/app.css">
</head>
<body class="public-body">
<?php if (empty($hideHeader)): ?>
<header class="public-header">
  <a class="brand" href="<?= $base ?>/index.php">
    <img src="<?= $base ?>/assets/logo_connecta.png" alt="Connecta">
    <span>
      <b>connecta</b>
      <small>AGENDA DE CONTATOS</small>
    </span>
  </a>
  <div class="actions">
    <a class="btn btn-ghost btn-sm" href="<?= $base ?>/index.php?url=login">Entrar</a>
    <a class="btn btn-primary btn-sm" href="<?= $base ?>/index.php?url=register">Criar Conta</a>
  </div>
</header>
<?php endif; ?>

<?= $content ?? '' ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= $base ?>/assets/app.js"></script>
</body>
</html>
