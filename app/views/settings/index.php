<div class="page-header">
  <div style="display:flex;align-items:center;gap:1rem">
    <div class="avatar" style="width:56px;height:56px;border-radius:50%;background:var(--primary-light);color:var(--primary-dark);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.25rem">
      <?= e(initials($user['name'] ?? '?')) ?>
    </div>
    <div>
      <h1 style="font-size:1.25rem;margin:0"><?= e($user['name'] ?? '') ?></h1>
      <p class="muted text-sm" style="margin:0"><?= e($user['company'] ?? '') ?> · <?= e($user['role'] ?? 'Usuário') ?></p>
    </div>
  </div>
</div>

<div class="settings-sections">
  <!-- Conta -->
  <div class="settings-section">
    <div class="settings-section-title"><i class="bi bi-person-gear"></i> CONFIGURAÇÕES DE CONTA</div>
    <form method="post" action="<?= BASE_URL ?>/index.php?url=settings/save">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="section" value="profile">
      <div class="settings-row">
        <div>
          <div class="label">Informações Pessoais</div>
          <div class="desc">Nome, empresa e cargo</div>
        </div>
      </div>
      <div style="padding:0 1.25rem 1rem">
        <div class="form-group">
          <label>Nome</label>
          <input class="form-control" name="name" value="<?= e($user['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Empresa</label>
          <input class="form-control" name="company" value="<?= e($user['company'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Cargo</label>
          <input class="form-control" name="role" value="<?= e($user['role'] ?? '') ?>">
        </div>
        <button class="btn btn-primary btn-sm" type="submit">Salvar</button>
      </div>
    </form>
    <div class="settings-row">
      <div>
        <div class="label">Senha & Segurança</div>
        <div class="desc">Altere sua senha de acesso</div>
      </div>
    </div>
    <form method="post" action="<?= BASE_URL ?>/index.php?url=settings/save" style="padding:0 1.25rem 1rem">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="section" value="password">
      <div class="form-group">
        <label>Senha atual</label>
        <input class="form-control" type="password" name="current_password">
      </div>
      <div class="form-group">
        <label>Nova senha</label>
        <input class="form-control" type="password" name="new_password" minlength="8">
      </div>
      <button class="btn btn-outline btn-sm" type="submit">Alterar senha</button>
    </form>
  </div>

  <!-- Notificações -->
  <form method="post" action="<?= BASE_URL ?>/index.php?url=settings/save" class="settings-section">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="section" value="notifications">
    <input type="hidden" name="theme" value="<?= e($user['theme'] ?? 'light') ?>">
    <div class="settings-section-title"><i class="bi bi-bell"></i> NOTIFICAÇÕES</div>
    <div class="settings-row">
      <div>
        <div class="label">Notificações</div>
        <div class="desc">Alertas no seu dispositivo</div>
      </div>
      <button type="submit" name="notify_device" value="1" class="toggle <?= !empty($user['notify_device']) ? 'on' : '' ?>"
              onclick="this.classList.toggle('on'); this.form.notify_device_hidden.value = this.classList.contains('on') ? '1' : '0'"></button>
      <input type="hidden" name="notify_device" id="notify_device_hidden" value="<?= !empty($user['notify_device']) ? '1' : '0' ?>">
    </div>
    <div class="settings-row">
      <div>
        <div class="label">Resumos por e-mail</div>
        <div class="desc">Resumo semanal das atividades</div>
      </div>
      <button type="button" class="toggle <?= !empty($user['notify_email']) ? 'on' : '' ?>"
              onclick="this.classList.toggle('on'); document.getElementById('notify_email_hidden').value = this.classList.contains('on') ? '1' : '0'"></button>
      <input type="hidden" name="notify_email" id="notify_email_hidden" value="<?= !empty($user['notify_email']) ? '1' : '0' ?>">
    </div>
    <div style="padding:.75rem 1.25rem">
      <button class="btn btn-primary btn-sm" type="submit">Salvar notificações</button>
    </div>
  </form>

  <!-- Privacidade -->
  <form method="post" action="<?= BASE_URL ?>/index.php?url=settings/save" class="settings-section">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="section" value="privacy">
    <input type="hidden" name="theme" value="<?= e($user['theme'] ?? 'light') ?>">
    <input type="hidden" name="notify_device" value="<?= !empty($user['notify_device']) ? '1' : '0' ?>">
    <input type="hidden" name="notify_email" value="<?= !empty($user['notify_email']) ? '1' : '0' ?>">
    <div class="settings-section-title"><i class="bi bi-eye"></i> PRIVACIDADE</div>
    <div class="settings-row">
      <div>
        <div class="label">Visibilidade do Perfil</div>
        <div class="desc">Todos podem ver o seu perfil</div>
      </div>
      <button type="button" class="toggle <?= !empty($user['profile_visible']) ? 'on' : '' ?>"
              onclick="this.classList.toggle('on'); document.getElementById('profile_visible_hidden').value = this.classList.contains('on') ? '1' : '0'"></button>
      <input type="hidden" name="profile_visible" id="profile_visible_hidden" value="<?= !empty($user['profile_visible']) ? '1' : '0' ?>">
    </div>
    <div class="settings-row">
      <div>
        <div class="label">Contatos Bloqueados</div>
        <div class="desc">Gerenciar lista de bloqueios</div>
      </div>
      <span style="color:var(--text-muted)">›</span>
    </div>
    <div style="padding:.75rem 1.25rem">
      <button class="btn btn-primary btn-sm" type="submit">Salvar privacidade</button>
    </div>
  </form>

  <!-- Suporte -->
  <div class="settings-section">
    <div class="settings-section-title"><i class="bi bi-life-preserver"></i> SUPORTE</div>
    <div class="settings-row">
      <div class="label">Termos de Serviço</div>
      <span style="color:var(--text-muted)">↗</span>
    </div>
    <div class="settings-row">
      <div class="label">Contratar Suporte</div>
      <span style="color:var(--text-muted)">›</span>
    </div>
  </div>

  <!-- Preferências -->
  <form method="post" action="<?= BASE_URL ?>/index.php?url=settings/save" class="settings-section">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="section" value="theme">
    <input type="hidden" name="notify_device" value="<?= !empty($user['notify_device']) ? '1' : '0' ?>">
    <input type="hidden" name="notify_email" value="<?= !empty($user['notify_email']) ? '1' : '0' ?>">
    <input type="hidden" name="profile_visible" value="<?= !empty($user['profile_visible']) ? '1' : '0' ?>">
    <div class="settings-section-title"><i class="bi bi-palette"></i> PREFERÊNCIA</div>
    <div class="settings-row">
      <div>
        <div class="label">Tema</div>
        <div class="desc">Claro ou escuro</div>
      </div>
      <select class="form-control" name="theme" style="width:auto;min-width:120px">
        <option value="light" <?= ($user['theme']??'light')==='light'?'selected':'' ?>>Claro</option>
        <option value="dark" <?= ($user['theme']??'')==='dark'?'selected':'' ?>>Escuro</option>
      </select>
    </div>
    <div class="settings-row">
      <div>
        <div class="label">Fonte</div>
        <div class="desc">Tamanho do texto</div>
      </div>
      <span style="color:var(--text-muted)">›</span>
    </div>
    <div style="padding:.75rem 1.25rem">
      <button class="btn btn-primary btn-sm" type="submit">Salvar preferências</button>
    </div>
  </form>
</div>
