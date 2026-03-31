<?php
// ============================================
// KeFix - Header Parcial (incluso em todas páginas)
// ============================================
require_once __DIR__ . '/config.php';
$pdo = conectar();
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
$total_carrinho = array_sum(array_column($_SESSION['carrinho'] ?? [], 'quantidade'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="site-url" content="<?= SITE_URL ?>">
  <title><?= $page_title ?? SITE_NAME ?></title>
  <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body>

<!-- Barra de anúncio -->
<div class="barra-anuncio">
  🚀 FRETE GRÁTIS em compras acima de R$ 299 para todo o Brasil!
</div>

<!-- Header -->
<header class="header">
  <div class="container header-inner">
    <!-- Logo -->
    <a href="<?= SITE_URL ?>/index.php" class="logo">
      <span class="logo-nome">KeFix</span>
      <small>Distribuidora</small>
    </a>

    <!-- Busca -->
    <form class="busca" action="<?= SITE_URL ?>/busca.php" method="GET">
      <input type="text" name="q" placeholder="Buscar peças, modelos..." value="<?= sanitizar($_GET['q'] ?? '') ?>">
      <button type="submit"><i data-lucide="search"></i></button>
    </form>

    <!-- Ações -->
    <div class="header-acoes">
      <?php if (usuario_logado()): ?>
        <a href="<?= SITE_URL ?>/minha-conta.php" class="btn-header">
          <i data-lucide="user"></i>
          <span><?= explode(' ', $_SESSION['usuario_nome'])[0] ?></span>
        </a>
        <a href="<?= SITE_URL ?>/php/auth.php?acao=sair" class="btn-header">
          <i data-lucide="log-out"></i>
        </a>
      <?php else: ?>
        <a href="<?= SITE_URL ?>/php/auth.php?pagina=login" class="btn-header">
          <i data-lucide="user"></i>
          <span>Entrar</span>
        </a>
      <?php endif; ?>

      <a href="<?= SITE_URL ?>/carrinho.php" class="btn-header btn-carrinho">
        <i data-lucide="shopping-cart"></i>
        <span class="badge-carrinho" id="badge-carrinho"><?= $total_carrinho > 0 ? $total_carrinho : '' ?></span>
      </a>

      <button class="btn-menu-mobile" onclick="toggleMenu()">
        <i data-lucide="menu"></i>
      </button>
    </div>
  </div>

  <!-- Menu categorias -->
  <nav class="nav-categorias" id="nav-menu">
    <div class="container nav-inner">
      <a href="<?= SITE_URL ?>/index.php">Início</a>
      <?php foreach ($categorias as $cat): ?>
        <a href="<?= SITE_URL ?>/categoria.php?slug=<?= $cat['slug'] ?>">
          <?= htmlspecialchars($cat['nome']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </nav>
</header>
