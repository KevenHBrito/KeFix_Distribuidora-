<?php
// ============================================
// KeFix Admin - Pedidos
// ============================================
require_once __DIR__ . '/../php/config.php';

// Processar requisição AJAX de atualização de status ANTES de verificar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['pedido_id'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    // Verificar autenticação para AJAX
    if (!admin_logado()) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Não autenticado.']);
        exit;
    }
    
    $pdo = conectar();
    $statuses = ['pendente','confirmado','enviado','entregue','cancelado'];
    
    if (in_array($_POST['status'], $statuses)) {
        try {
            $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
            $stmt->execute([sanitizar($_POST['status']), (int)$_POST['pedido_id']]);
            echo json_encode(['sucesso' => true]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar.']);
        }
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Status inválido.']);
    }
    exit;
}

// Verificar autenticação para requisições normais
if (!admin_logado()) redirecionar('/php/auth.php?pagina=login');

$pdo = conectar();

// Ver pedido específico
$ver_id = (int)($_GET['ver'] ?? 0);
$pedido_detalhes = null;
$itens_pedido = [];
if ($ver_id) {
    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
    $stmt->execute([$ver_id]);
    $pedido_detalhes = $stmt->fetch();
    if ($pedido_detalhes) {
        $stmt2 = $pdo->prepare("
            SELECT ip.*, p.nome AS produto_nome, p.imagem
            FROM itens_pedido ip
            JOIN produtos p ON p.id = ip.produto_id
            WHERE ip.pedido_id = ?
        ");
        $stmt2->execute([$ver_id]);
        $itens_pedido = $stmt2->fetchAll();
    }
}

// Listar pedidos
$filtro_status = sanitizar($_GET['status'] ?? '');
if ($filtro_status) {
    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE status = ? ORDER BY criado_em DESC");
    $stmt->execute([$filtro_status]);
} else {
    $stmt = $pdo->query("SELECT * FROM pedidos ORDER BY criado_em DESC");
}
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="site-url" content="<?= SITE_URL ?>">
  <title>Pedidos - Admin KeFix</title>
  <link rel="stylesheet" href="<?= SITE_URL ?>/css/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <script>
    // Definir SITE_URL IMEDIATAMENTE antes de qualquer outro script
    window.SITE_URL = '<?= SITE_URL ?>';
    console.log('window.SITE_URL definido:', window.SITE_URL);
  </script>
</head>
<body class="admin-body">
<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-topbar">
      <h1>Pedidos</h1>
      <?php if ($pedido_detalhes): ?><a href="?" class="btn-outline">← Voltar</a><?php endif; ?>
    </div>

    <?php if ($pedido_detalhes): ?>
    <!-- DETALHE DO PEDIDO -->
    <div class="admin-card">
      <h2>Pedido #<?= str_pad($pedido_detalhes['id'],5,'0',STR_PAD_LEFT) ?></h2>
      <div class="detalhe-grid">
        <div><strong>Cliente:</strong> <?= htmlspecialchars($pedido_detalhes['nome_cliente']) ?></div>
        <div><strong>Telefone:</strong> <?= htmlspecialchars($pedido_detalhes['telefone']) ?></div>
        <div><strong>Pagamento:</strong> <?= ucfirst($pedido_detalhes['forma_pagamento']) ?></div>
        <div><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido_detalhes['criado_em'])) ?></div>
        <div style="grid-column:1/-1"><strong>Endereço:</strong> <?= htmlspecialchars($pedido_detalhes['endereco']) ?></div>
        <?php if ($pedido_detalhes['observacoes']): ?>
        <div style="grid-column:1/-1"><strong>Observações:</strong> <?= htmlspecialchars($pedido_detalhes['observacoes']) ?></div>
        <?php endif; ?>
      </div>
      <table class="tabela" style="margin-top:1.5rem">
        <thead><tr><th>Produto</th><th>Qtd</th><th>Preço un.</th><th>Subtotal</th></tr></thead>
        <tbody>
          <?php foreach ($itens_pedido as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['produto_nome']) ?></td>
            <td><?= $item['quantidade'] ?></td>
            <td><?= formatar_preco($item['preco_unitario']) ?></td>
            <td><?= formatar_preco($item['preco_unitario'] * $item['quantidade']) ?></td>
          </tr>
          <?php endforeach; ?>
          <tr><td colspan="3" style="text-align:right"><strong>Total:</strong></td><td><strong><?= formatar_preco($pedido_detalhes['total']) ?></strong></td></tr>
        </tbody>
      </table>
    </div>

    <?php else: ?>
    <!-- LISTA DE PEDIDOS -->
    <div class="admin-card">
      <div style="margin-bottom:1rem;display:flex;gap:.5rem;flex-wrap:wrap">
        <a href="?" class="btn-sm <?= !$filtro_status?'ativo':'' ?>">Todos</a>
        <?php foreach(['pendente','confirmado','enviado','entregue','cancelado'] as $s): ?>
          <a href="?status=<?= $s ?>" class="btn-sm <?= $filtro_status===$s?'ativo':'' ?>"><?= ucfirst($s) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="tabela-wrap">
        <table class="tabela">
          <thead><tr><th>#</th><th>Cliente</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Data</th><th>Ver</th></tr></thead>
          <tbody>
            <?php foreach ($pedidos as $p): ?>
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
              <td><a href="?ver=<?= $p['id'] ?>" class="btn-sm">Ver</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </main>
</div>
<script src="<?= SITE_URL ?>/js/admin.js"></script>
<script>lucide.createIcons();</script>
</body></html>
