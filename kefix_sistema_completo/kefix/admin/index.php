<?php
// ============================================
// KeFix Admin - Dashboard
// ============================================
require_once __DIR__ . '/../php/config.php';
if (!admin_logado()) redirecionar('/php/auth.php?pagina=login');

$pdo = conectar();
$total_produtos = $pdo->query("SELECT COUNT(*) FROM produtos WHERE ativo=1")->fetchColumn();
$total_pedidos  = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo='cliente'")->fetchColumn();
$faturamento    = $pdo->query("SELECT SUM(total) FROM pedidos WHERE status != 'cancelado'")->fetchColumn() ?? 0;
$pedidos_recentes = $pdo->query("SELECT * FROM pedidos ORDER BY criado_em DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="site-url" content="<?= SITE_URL ?>">
  <title>Admin - KeFix</title>
  <script>
    window.SITE_URL = '<?= SITE_URL ?>';
    console.log('window.SITE_URL definido:', window.SITE_URL);
  </script>
  <link rel="stylesheet" href="<?= SITE_URL ?>/css/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="admin-body">

<div class="admin-layout">
  <!-- Sidebar -->
  <?php include __DIR__ . '/sidebar.php'; ?>

  <!-- Conteúdo -->
  <main class="admin-main">
    <div class="admin-topbar">
      <h1>Dashboard</h1>
      <span>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</span>
    </div>

    <!-- Cards de estatísticas -->
    <div class="stats-grid">
      <div class="stat-card azul">
        <i data-lucide="package"></i>
        <div>
          <strong><?= $total_produtos ?></strong>
          <span>Produtos ativos</span>
        </div>
      </div>
      <div class="stat-card verde">
        <i data-lucide="shopping-bag"></i>
        <div>
          <strong><?= $total_pedidos ?></strong>
          <span>Total de pedidos</span>
        </div>
      </div>
      <div class="stat-card laranja">
        <i data-lucide="users"></i>
        <div>
          <strong><?= $total_usuarios ?></strong>
          <span>Clientes</span>
        </div>
      </div>
      <div class="stat-card roxo">
        <i data-lucide="trending-up"></i>
        <div>
          <strong><?= formatar_preco($faturamento) ?></strong>
          <span>Faturamento total</span>
        </div>
      </div>
    </div>

    <!-- Pedidos recentes -->
    <div class="admin-card" style="margin-top:2rem">
      <div class="admin-card-header">
        <h2>Pedidos recentes</h2>
        <a href="<?= SITE_URL ?>/admin/pedidos.php" class="btn-sm">Ver todos</a>
      </div>
      <div class="tabela-wrap">
        <table class="tabela">
          <thead>
            <tr><th>#</th><th>Cliente</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Data</th><th>Ação</th></tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos_recentes as $p): ?>
            <tr>
              <td>#<?= str_pad($p['id'],5,'0',STR_PAD_LEFT) ?></td>
              <td><?= htmlspecialchars($p['nome_cliente']) ?></td>
              <td><?= formatar_preco($p['total']) ?></td>
              <td><?= ucfirst($p['forma_pagamento']) ?></td>
              <td>
                <select onchange="atualizarStatus(<?= $p['id'] ?>, this.value)" class="select-status status-<?= $p['status'] ?>">
                  <?php foreach(['pendente','confirmado','enviado','entregue','cancelado'] as $s): ?>
                    <option value="<?= $s ?>" <?= $p['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><?= date('d/m/Y', strtotime($p['criado_em'])) ?></td>
              <td><a href="<?= SITE_URL ?>/admin/pedidos.php?ver=<?= $p['id'] ?>" class="btn-sm">Ver</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<script src="<?= SITE_URL ?>/js/admin.js"></script>
<script>lucide.createIcons();</script>
</body>
</html>
