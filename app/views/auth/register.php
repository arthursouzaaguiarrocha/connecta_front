<div class="auth-page">
  <div class="auth-card">
    <a class="brand" href="<?= BASE_URL ?>/index.php">
      <img src="<?= BASE_URL ?>/assets/logo_connecta.png" alt="">
      <span>
        <b>connecta</b>
        <small>AGENDA DE CONTATOS</small>
      </span>
    </a>
    <h1>Criar Conta</h1>
    <p class="subtitle">Comece a organizar sua agenda agora</p>
    <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div class="form-group">
        <label>Nome completo</label>
        <input class="form-control" type="text" name="name" placeholder="Seu nome" required>
      </div>
      <div class="form-group">
        <label>E-mail</label>
        <input class="form-control" type="email" name="email" placeholder="nome@empresa.com" required>
      </div>
      <div class="form-group">
        <label>Empresa (opcional)</label>
        <input class="form-control" type="text" name="company" placeholder="Empresa / Mercado Livre">
      </div>
      <div class="form-group">
        <label>Senha</label>
        <input class="form-control" type="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
      </div>
      <button class="btn btn-primary btn-full" type="submit">Criar minha conta</button>
    </form>
    <p class="auth-switch" style="margin-top:1.25rem">Já tem conta? <a href="<?= BASE_URL ?>/index.php?url=login">Entrar</a></p>
  </div>
</div>
