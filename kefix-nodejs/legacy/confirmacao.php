<?php
// ============================================
// KeFix - Confirmação de Pedido
// ============================================
require_once __DIR__ . '/php/config.php';
$page_title = 'Pedido Confirmado - KeFix';

$pedido_id = $_SESSION['pedido_confirmado'] ?? null;
if (!$pedido_id) redirecionar('/index.php');
unset($_SESSION['pedido_confirmado']);

$pdo = conectar();
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
$stmt->execute([$pedido_id]);
$pedido = $stmt->fetch();

require_once __DIR__ . '/php/header_partial.php';
?>

<main class="container confirmacao-main">
  <div class="confirmacao-card">
    <div class="confirmacao-icone">✅</div>
    <h1>Pedido confirmado!</h1>
    <p>Obrigado, <strong><?= htmlspecialchars($pedido['nome_cliente']) ?></strong>! Seu pedido foi recebido com sucesso.</p>

    <div class="confirmacao-detalhes">
      <div><span>Número do pedido</span><strong>#<?= str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) ?></strong></div>
      <div><span>Total</span><strong><?= formatar_preco($pedido['total']) ?></strong></div>
      <div><span>Pagamento</span><strong><?= ucfirst($pedido['forma_pagamento']) ?></strong></div>
      <div><span>Status</span><strong class="status-<?= $pedido['status'] ?>"><?= ucfirst($pedido['status']) ?></strong></div>
    </div>

    <?php if ($pedido['forma_pagamento'] === 'pix'): ?>
    <div class="pix-info">
      <p>🔑 Chave PIX: <strong>contato@kefix.com.br</strong></p>
      <p>Envie o comprovante para nosso WhatsApp para agilizar o envio.</p>
    </div>
    <?php endif; ?>

    <div style="margin-top:2rem;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="<?= SITE_URL ?>/index.php" class="btn-primary">Continuar comprando</a>
      <?php if (usuario_logado()): ?>
      <a href="<?= SITE_URL ?>/minha-conta.php" class="btn-outline">Meus pedidos</a>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/php/footer_partial.php'; ?>
