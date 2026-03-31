<?php
// ============================================
// KeFix - Minha Conta / Meus Pedidos
// ============================================
require_once __DIR__ . '/php/config.php';
if (!usuario_logado()) redirecionar('/php/auth.php?pagina=login');

$page_title = 'Minha Conta - KeFix';
$pdo = conectar();

$pedidos = $pdo->prepare("
    SELECT p.*, COUNT(ip.id) AS total_itens
    FROM pedidos p
    LEFT JOIN itens_pedido ip ON ip.pedido_id = p.id
    WHERE p.usuario_id = ?
    GROUP BY p.id
    ORDER BY p.criado_em DESC
");
$pedidos->execute([$_SESSION['usuario_id']]);
$meus_pedidos = $pedidos->fetchAll();

require_once __DIR__ . '/php/header_partial.php';
?>

<main class="container" style="padding:2rem 1rem 3rem">
  <h1 class="secao-titulo">Minha Conta</h1>
  <p style="margin-bottom:2rem">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong>!</p>

  <h2 style="font-size:1.2rem;margin-bottom:1rem">Meus Pedidos</h2>

  <?php if (empty($meus_pedidos)): ?>
    <div class="vazio">
      <i data-lucide="package" style="width:48px;height:48px;color:#ccc"></i>
      <p>Você ainda não fez nenhum pedido.</p>
      <a href="<?= SITE_URL ?>/index.php" class="btn-primary">Fazer primeiro pedido</a>
    </div>
  <?php else: ?>
  <div class="tabela-wrap">
    <table class="tabela">
      <thead>
        <tr>
          <th>Pedido</th>
          <th>Data</th>
          <th>Itens</th>
          <th>Total</th>
          <th>Pagamento</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($meus_pedidos as $p): ?>
        <tr>
          <td><strong>#<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
          <td><?= date('d/m/Y', strtotime($p['criado_em'])) ?></td>
          <td><?= $p['total_itens'] ?> item(s)</td>
          <td><?= formatar_preco($p['total']) ?></td>
          <td><?= ucfirst($p['forma_pagamento']) ?></td>
          <td><span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <div style="margin-top:2rem">
    <a href="<?= SITE_URL ?>/php/auth.php?acao=sair" class="btn-outline">Sair da conta</a>
  </div>
</main>

<?php require_once __DIR__ . '/php/footer_partial.php'; ?>
