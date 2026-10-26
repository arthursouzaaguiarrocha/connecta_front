<section class="hero">
  <div class="hero-copy">
    <span class="eyebrow"><i class="bi bi-lightning-charge"></i> Gestão de contatos inteligente</span>
    <h1>Seu tempo é valioso.<br><span class="accent">Connecta cuida da agenda.</span></h1>
    <p>Organize seus contatos, agendamentos e compromissos de forma simples, rápida e segura.</p>
    <div class="hero-actions">
      <a class="btn btn-dark btn-lg" href="<?= BASE_URL ?>/index.php?url=register">Começar agora <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="hero-trust">
      <span><i class="bi bi-check-circle-fill"></i> Fácil de usar</span>
      <span><i class="bi bi-shield-check"></i> Seus dados protegidos</span>
    </div>
  </div>
  <div class="hero-visual">
    <div style="background:linear-gradient(145deg,#f0fdfa,#e0f2fe);border-radius:20px;padding:2rem;box-shadow:0 20px 50px rgba(15,23,42,.12);max-width:420px">
      <div style="background:#fff;border-radius:12px;padding:1rem;box-shadow:0 4px 12px rgba(0,0,0,.06)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
          <strong style="font-size:.9rem">Contatos</strong>
          <span style="background:#0D9488;color:#fff;font-size:.7rem;padding:.2rem .6rem;border-radius:999px"><i class="bi bi-plus"></i> Novo</span>
        </div>
        <?php foreach ([
          ['AS','Arthur Souza','+55 63 99988-9879'],
          ['GM','Gustavo Milh.','+55 63 99988-6545'],
          ['BA','Beatriz Alves','+55 63 99234-5678'],
        ] as $c): ?>
        <div style="display:flex;align-items:center;gap:.75rem;padding:.6rem 0;border-bottom:1px solid #f1f5f9">
          <span style="width:36px;height:36px;border-radius:50%;background:#ccfbf1;color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem"><?= $c[0] ?></span>
          <div>
            <b style="font-size:.85rem;display:block"><?= $c[1] ?></b>
            <small style="color:#64748b;font-size:.75rem"><?= $c[2] ?></small>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<div class="features-bar">
  <div class="feature-item">
    <div class="fi"><i class="bi bi-shield-lock"></i></div>
    <span>Segurança de verdade</span>
  </div>
  <div class="feature-item">
    <div class="fi"><i class="bi bi-people"></i></div>
    <span>Centralize os seus contatos</span>
  </div>
  <div class="feature-item">
    <div class="fi"><i class="bi bi-phone"></i></div>
    <span>Acesso de qualquer lugar</span>
  </div>
</div>
