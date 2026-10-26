<div class="page-header">
  <div>
    <h1>CONTATOS</h1>
  </div>
  <div class="actions">
    <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=contacts/form"><i class="bi bi-person-plus"></i> Criar Contato</a>
    <a class="btn btn-dark btn-sm" href="<?= BASE_URL ?>/index.php?url=groups"><i class="bi bi-megaphone"></i> Lista de transmissão</a>
  </div>
</div>

<?php if (!$contacts): ?>
  <div class="card">
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-people"></i></div>
      <h3><?= $search ? 'Nenhum contato encontrado' : 'Sua agenda está vazia' ?></h3>
      <p><?= $search ? 'Tente outro termo de busca.' : 'Adicione seu primeiro contato para começar.' ?></p>
      <?php if (!$search): ?>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=contacts/form"><i class="bi bi-person-plus"></i> Adicionar contato</a>
      <?php endif; ?>
    </div>
  </div>
<?php else: ?>
  <div class="contacts-grid">
    <?php foreach ($contacts as $c): ?>
      <article class="contact-card">
        <div class="contact-card-top">
          <div>
            <div class="name"><?= e($c['name']) ?></div>
            <div class="phone"><?= e(format_phone($c['phone'])) ?></div>
            <?php if ($c['email']): ?><div class="email"><?= e($c['email']) ?></div><?php endif; ?>
          </div>
          <span class="badge badge-success"><?= e($c['status'] ?? 'Ativo') ?></span>
        </div>
        <div class="contact-socials">
          <?php if ($c['whatsapp']): ?><span class="social-icon whatsapp" title="WhatsApp"><i class="bi bi-whatsapp"></i></span><?php endif; ?>
          <?php if ($c['email']): ?><span class="social-icon email" title="E-mail"><i class="bi bi-envelope"></i></span><?php endif; ?>
          <?php if (!empty($c['teams'])): ?><span class="social-icon teams" title="Teams"><i class="bi bi-microsoft-teams"></i></span><?php endif; ?>
          <?php if (!empty($c['telegram'])): ?><span class="social-icon telegram" title="Telegram"><i class="bi bi-telegram"></i></span><?php endif; ?>
        </div>
        <div class="contact-actions">
          <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/index.php?url=contacts/form&id=<?= (int)$c['id'] ?>"><i class="bi bi-pencil"></i> Editar</a>
          <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/index.php?url=contacts/form&id=<?= (int)$c['id'] ?>"><i class="bi bi-eye"></i> Ver</a>
          <form method="post" action="<?= BASE_URL ?>/index.php?url=contacts/delete" class="delete-form" style="display:inline">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
            <button type="submit" class="btn btn-ghost btn-sm" title="Excluir" style="color:var(--danger)"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
