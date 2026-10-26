<div class="auth-page">
  <div class="auth-card">
    <a class="brand" href="<?= BASE_URL ?>/index.php">
      <img src="<?= BASE_URL ?>/assets/logo_connecta.png" alt="">
      <span>
        <b>connecta</b>
        <small>AGENDA DE CONTATOS</small>
      </span>
    </a>
    <h1>Login</h1>
    <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div class="form-group">
        <label>E-mail</label>
        <div class="input-icon">
          <span class="icon"><i class="bi bi-envelope"></i></span>
          <input class="form-control" type="email" name="email" placeholder="nome@empresa.com" required autocomplete="email">
        </div>
      </div>
      <div class="form-group">
        <div class="form-footer" style="margin-bottom:.35rem">
          <label style="margin:0">Senha</label>
          <a href="#" style="font-size:.8rem">Esqueci minha senha</a>
        </div>
        <div class="input-icon">
          <span class="icon"><i class="bi bi-lock"></i></span>
          <input class="form-control" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
        </div>
      </div>
      <button class="btn btn-primary btn-full" type="submit">ENTRAR</button>
    </form>
    <div class="divider">ou</div>
    <div class="social-btns">
      <button type="button" class="social-btn" title="Google"><i class="bi bi-google"></i></button>
      <button type="button" class="social-btn" title="Facebook"><i class="bi bi-facebook"></i></button>
      <button type="button" class="social-btn" title="Apple"><i class="bi bi-apple"></i></button>
    </div>
    <p class="auth-switch">Não tem uma conta? <a href="<?= BASE_URL ?>/index.php?url=register">Criar Conta</a></p>
  </div>
</div>
