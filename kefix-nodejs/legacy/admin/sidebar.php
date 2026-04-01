<aside class="admin-sidebar">
  <div class="sidebar-logo">
    <span class="logo-nome">KeFix</span>
    <small>Admin</small>
  </div>
  <nav class="sidebar-nav">
    <a href="<?= SITE_URL ?>/admin/index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'ativo':'' ?>">
      <i data-lucide="layout-dashboard"></i> Dashboard
    </a>
    <a href="<?= SITE_URL ?>/admin/produtos.php" class="<?= basename($_SERVER['PHP_SELF'])=='produtos.php'?'ativo':'' ?>">
      <i data-lucide="package"></i> Produtos
    </a>
    <a href="<?= SITE_URL ?>/admin/pedidos.php" class="<?= basename($_SERVER['PHP_SELF'])=='pedidos.php'?'ativo':'' ?>">
      <i data-lucide="shopping-bag"></i> Pedidos
    </a>
    <a href="<?= SITE_URL ?>/admin/categorias.php" class="<?= basename($_SERVER['PHP_SELF'])=='categorias.php'?'ativo':'' ?>">
      <i data-lucide="tag"></i> Categorias
    </a>
    <hr style="border-color:#334155;margin:.5rem 0">
    <a href="<?= SITE_URL ?>/index.php" target="_blank">
      <i data-lucide="external-link"></i> Ver site
    </a>
    <a href="<?= SITE_URL ?>/php/auth.php?acao=sair">
      <i data-lucide="log-out"></i> Sair
    </a>
  </nav>
</aside>
