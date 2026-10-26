<div class="page-header">
  <div>
    <h1><?= $id ? 'Editar contato' : 'Novo contato' ?></h1>
  </div>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?url=contacts">← Voltar</a>
</div>

<div class="card" style="max-width:560px">
  <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="form-group">
      <label>Nome *</label>
      <input class="form-control" name="name" value="<?= e($contact['name']) ?>" required>
    </div>
    <div class="form-group">
      <label>Telefone *</label>
      <input class="form-control" name="phone" value="<?= e($contact['phone']) ?>" required>
    </div>
    <div class="form-group">
      <label>E-mail</label>
      <input class="form-control" type="email" name="email" value="<?= e($contact['email'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>WhatsApp</label>
      <input class="form-control" name="whatsapp" value="<?= e($contact['whatsapp'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Telegram</label>
      <input class="form-control" name="telegram" value="<?= e($contact['telegram'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Teams</label>
      <input class="form-control" name="teams" value="<?= e($contact['teams'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Endereço</label>
      <input class="form-control" name="address" value="<?= e($contact['address'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Observações</label>
      <textarea class="form-control" name="notes" rows="3"><?= e($contact['notes'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>Status</label>
      <select class="form-control" name="status">
        <option value="Ativo" <?= ($contact['status'] ?? '') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
        <option value="Inativo" <?= ($contact['status'] ?? '') === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
      </select>
    </div>
    <button class="btn btn-primary" type="submit"><?= $id ? 'Salvar alterações' : 'Criar contato' ?></button>
  </form>
</div>
