<?php
// ============================================
// KeFix - Página Inicial
// ============================================
require_once __DIR__ . '/php/config.php';
$page_title = 'KeFix Distribuidora - Peças para Celular';

$pdo = conectar();

// Produtos em destaque
$destaques = $pdo->query("
    SELECT p.*, c.nome AS categoria_nome
    FROM produtos p
    JOIN categorias c ON p.categoria_id = c.id
    WHERE p.destaque = 1 AND p.ativo = 1
    LIMIT 8
")->fetchAll();

// Categorias
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();

require_once __DIR__ . '/php/header_partial.php';
?>

<main>
  <!-- Banner Hero -->
  <section class="hero">
    <div class="hero-content">
      <div class="hero-texto">
        <span class="hero-badge">🔧 Distribuidora Oficial</span>
        <h1>Peças originais para <span class="destaque-texto">qualquer celular</span></h1>
        <p>Telas, baterias, conectores e muito mais. Entrega rápida para todo o Brasil com garantia de qualidade.</p>
        <div class="hero-botoes">
          <a href="#produtos" class="btn-primary">Ver produtos</a>
          <a href="<?= SITE_URL ?>/php/auth.php?pagina=cadastro" class="btn-outline">Criar conta grátis</a>
        </div>
        <div class="hero-stats">
          <div><strong>500+</strong><span>Produtos</span></div>
          <div><strong>99%</strong><span>Satisfação</span></div>
          <div><strong>24h</strong><span>Entrega SP</span></div>
        </div>
      </div>
      <div class="hero-img">
        <div class="hero-circle">
          <i data-lucide="smartphone" style="width:80px;height:80px;color:#007BFF"></i>
        </div>
      </div>
    </div>
  </section>

  <!-- Categorias -->
  <section class="secao-categorias">
    <div class="container">
      <h2 class="secao-titulo">Categorias</h2>
      <div class="grid-categorias">
        <?php foreach ($categorias as $cat): ?>
        <a href="<?= SITE_URL ?>/categoria.php?slug=<?= $cat['slug'] ?>" class="card-categoria">
          <i data-lucide="<?= htmlspecialchars($cat['icone']) ?>"></i>
          <span><?= htmlspecialchars($cat['nome']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Produtos em Destaque -->
  <section class="secao-produtos" id="produtos">
    <div class="container">
      <div class="secao-header">
        <h2 class="secao-titulo">Produtos em Destaque</h2>
        <a href="<?= SITE_URL ?>/categoria.php" class="ver-todos">Ver todos <i data-lucide="arrow-right" style="width:16px;height:16px"></i></a>
      </div>
      <div class="grid-produtos">
        <?php foreach ($destaques as $p): ?>
        <div class="card-produto">
          <a href="<?= SITE_URL ?>/produto.php?id=<?= $p['id'] ?>" class="card-img">
            <img src="<?= SITE_URL ?>/images/produtos/<?= htmlspecialchars($p['imagem']) ?>"
                 alt="<?= htmlspecialchars($p['nome']) ?>"
                 onerror="this.src='<?= SITE_URL ?>/images/sem-imagem.png'">
            <?php if ($p['estoque'] == 0): ?>
              <span class="badge-esgotado">Esgotado</span>
            <?php endif; ?>
          </a>
          <div class="card-info">
            <span class="card-categoria"><?= htmlspecialchars($p['categoria_nome']) ?></span>
            <h3><a href="<?= SITE_URL ?>/produto.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></a></h3>
            <div class="card-rodape">
              <strong class="card-preco"><?= formatar_preco($p['preco']) ?></strong>
              <?php if ($p['estoque'] > 0): ?>
              <button class="btn-add-carrinho" onclick="adicionarCarrinho(<?= $p['id'] ?>)">
                <i data-lucide="shopping-cart" style="width:16px;height:16px"></i>
              </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Benefícios -->
  <section class="secao-beneficios">
    <div class="container grid-beneficios">
      <div class="beneficio">
        <i data-lucide="truck"></i>
        <h4>Frete Rápido</h4>
        <p>Enviamos para todo Brasil com rastreamento</p>
      </div>
      <div class="beneficio">
        <i data-lucide="shield-check"></i>
        <h4>Garantia</h4>
        <p>Todos os produtos com garantia de qualidade</p>
      </div>
      <div class="beneficio">
        <i data-lucide="refresh-ccw"></i>
        <h4>Trocas fáceis</h4>
        <p>Política de troca simples e sem burocracia</p>
      </div>
      <div class="beneficio">
        <i data-lucide="headphones"></i>
        <h4>Suporte</h4>
        <p>Atendimento via WhatsApp e e-mail</p>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/php/footer_partial.php'; ?>
