<?php
// ============================================
// KeFix - Carrinho de Compras
// ============================================
require_once __DIR__ . '/php/config.php';
$page_title = 'Carrinho - KeFix';

$carrinho = $_SESSION['carrinho'] ?? [];
$total = 0;
foreach ($carrinho as $item) {
    $total += $item['preco'] * $item['quantidade'];
}

$erro = $_SESSION['erro_checkout'] ?? '';
unset($_SESSION['erro_checkout']);

// Recuperar dados salvos - primeiro da sessão, depois do banco se logado
$dados_endereco = $_SESSION['dados_pedido'] ?? [];
$nome_salvo = $dados_endereco['nome'] ?? (usuario_logado() ? htmlspecialchars($_SESSION['usuario_nome']) : '');
$telefone_salvo = $dados_endereco['telefone'] ?? '';
$rua_salvo = $dados_endereco['rua'] ?? '';
$numero_salvo = $dados_endereco['numero'] ?? '';
$bairro_salvo = $dados_endereco['bairro'] ?? '';
$cidade_salvo = $dados_endereco['cidade'] ?? '';
$estado_salvo = $dados_endereco['estado'] ?? '';

// Se usuário está logado e não há dados na sessão, buscar do banco
if (usuario_logado() && empty($dados_endereco)) {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT nome, telefone, endereco FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $nome_salvo = htmlspecialchars($user['nome'] ?? '');
        $telefone_salvo = htmlspecialchars($user['telefone'] ?? '');
        
        // Extrair endereço se estiver no formato: Rua, Número - Bairro, Cidade - Estado
        if ($user['endereco']) {
            $endereco_partes = explode(' - ', $user['endereco']);
            if (count($endereco_partes) >= 2) {
                // Parte 1: Rua, Número
                $rua_numero = explode(', ', $endereco_partes[0]);
                if (count($rua_numero) >= 2) {
                    $rua_salvo = htmlspecialchars(trim($rua_numero[0]));
                    $numero_salvo = htmlspecialchars(trim($rua_numero[1]));
                }
                // Parte 2: Bairro, Cidade
                if (count($endereco_partes) >= 2) {
                    $bairro_cidade = explode(', ', $endereco_partes[1]);
                    if (count($bairro_cidade) >= 1) {
                        $bairro_salvo = htmlspecialchars(trim($bairro_cidade[0]));
                    }
                    if (count($bairro_cidade) >= 2) {
                        $cidade_estado = explode(' - ', $bairro_cidade[1]);
                        $cidade_salvo = htmlspecialchars(trim($cidade_estado[0] ?? ''));
                        $estado_salvo = htmlspecialchars(trim($cidade_estado[1] ?? ''));
                    }
                }
            }
        }
    }
}

require_once __DIR__ . '/php/header_partial.php';
?>

<main class="container carrinho-main">
  <h1 class="secao-titulo" style="margin-bottom:1.5rem">🛒 Meu Carrinho</h1>

  <?php if ($erro): ?>
    <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <?php if (empty($carrinho)): ?>
    <div class="vazio">
      <i data-lucide="shopping-cart" style="width:64px;height:64px;color:#ccc"></i>
      <p>Seu carrinho está vazio.</p>
      <a href="<?= SITE_URL ?>/index.php" class="btn-primary">Continuar comprando</a>
    </div>
  <?php else: ?>
  <div class="carrinho-layout">

    <!-- Itens -->
    <div class="carrinho-itens" id="carrinho-itens">
      <?php foreach ($carrinho as $item): ?>
      <div class="item-carrinho" id="item-<?= $item['produto_id'] ?>">
        <img src="<?= SITE_URL ?>/images/produtos/<?= htmlspecialchars($item['imagem']) ?>"
             alt="<?= htmlspecialchars($item['nome']) ?>"
             onerror="this.src='<?= SITE_URL ?>/images/sem-imagem.png'">
        <div class="item-info">
          <h4><?= htmlspecialchars($item['nome']) ?></h4>
          <span><?= formatar_preco($item['preco']) ?> un.</span>
        </div>
        <div class="item-qty">
          <button onclick="atualizarQty(<?= $item['produto_id'] ?>, -1)">-</button>
          <span id="qty-<?= $item['produto_id'] ?>"><?= $item['quantidade'] ?></span>
          <button onclick="atualizarQty(<?= $item['produto_id'] ?>, 1)">+</button>
        </div>
        <div class="item-subtotal" id="sub-<?= $item['produto_id'] ?>">
          <?= formatar_preco($item['preco'] * $item['quantidade']) ?>
        </div>
        <button class="btn-remover" onclick="removerItem(<?= $item['produto_id'] ?>)" title="Remover">
          <i data-lucide="trash-2" style="width:18px;height:18px"></i>
        </button>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Resumo + Checkout -->
    <div class="carrinho-resumo">
      <h3>Resumo do pedido</h3>
      <div class="resumo-linha">
        <span>Subtotal</span>
        <strong id="total-resumo"><?= formatar_preco($total) ?></strong>
      </div>
      <div class="resumo-linha">
        <span>Frete</span>
        <span><?= $total >= 299 ? '<strong style="color:#28a745">Grátis</strong>' : 'Calculado no checkout' ?></span>
      </div>
      <hr>

      <form method="POST" action="<?= SITE_URL ?>/php/finalizar_pedido.php" id="form-checkout">
        <h3 style="margin-top:1.5rem">Dados de entrega</h3>
        <div class="campo">
          <label>Nome completo</label>
          <input type="text" name="nome" required value="<?= $nome_salvo ?>">
        </div>
        <div class="campo">
          <label>Telefone</label>
          <input type="tel" name="telefone" required placeholder="(44) 99999-9999" value="<?= $telefone_salvo ?>">
        </div>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
          <div class="campo">
            <label>Rua</label>
            <input type="text" name="rua" required placeholder="Ex: Avenida Paulista" value="<?= $rua_salvo ?>">
          </div>
          <div class="campo">
            <label>Número</label>
            <input type="text" name="numero" required placeholder="Ex: 1000" value="<?= $numero_salvo ?>">
          </div>
        </div>
        <div class="campo">
          <label>Bairro</label>
          <input type="text" name="bairro" required placeholder="Ex: Centro" value="<?= $bairro_salvo ?>">
        </div>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
          <div class="campo">
            <label>Cidade</label>
            <input type="text" name="cidade" required placeholder="Ex: São Paulo" value="<?= $cidade_salvo ?>">
          </div>
          <div class="campo">
            <label>Estado</label>
            <input type="text" name="estado" required placeholder="Ex: SP" maxlength="2" value="<?= $estado_salvo ?>">
          </div>
        </div>
        <div class="campo">
          <label>Complemento</label>
          <textarea name="observacoes" placeholder="Alguma instrução especial?"></textarea>
        </div>

        <h3 style="margin-top:1.5rem">Forma de pagamento</h3>
        <div class="pagamentos">
          <label class="pagamento-opcao">
            <input type="radio" name="pagamento" value="pix" required>
            <span>💸 PIX</span>
          </label>
          <label class="pagamento-opcao">
            <input type="radio" name="pagamento" value="cartao">
            <span>💳 Cartão de crédito/débito</span>
          </label>
          <label class="pagamento-opcao">
            <input type="radio" name="pagamento" value="dinheiro">
            <span>💵 Dinheiro na retirada</span>
          </label>
        </div>

        <button type="submit" class="btn-primary btn-full" style="margin-top:1.5rem">
          Finalizar pedido →
        </button>
      </form>

      <a href="<?= SITE_URL ?>/index.php" class="btn-outline btn-full" style="margin-top:.75rem;display:block;text-align:center">
        ← Continuar comprando
      </a>
    </div>
  </div>
  <?php endif; ?>
</main>

<script>
// Preços do carrinho para cálculo no cliente
const precos = {
  <?php foreach ($carrinho as $item): ?>
  <?= $item['produto_id'] ?>: <?= $item['preco'] ?>,
  <?php endforeach; ?>
};

function atualizarQty(id, delta) {
  const span = document.getElementById('qty-' + id);
  let qty = parseInt(span.textContent) + delta;
  if (qty < 1) { removerItem(id); return; }
  span.textContent = qty;

  // Atualizar subtotal no cliente
  const sub = document.getElementById('sub-' + id);
  if (sub && precos[id]) {
    sub.textContent = 'R$ ' + (precos[id] * qty).toFixed(2).replace('.', ',');
  }

  fetch('<?= SITE_URL ?>/php/carrinho.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `acao=atualizar&produto_id=${id}&quantidade=${qty}`
  }).then(r => r.json()).then(d => {
    document.getElementById('total-resumo').textContent = 'R$ ' + parseFloat(d.total).toFixed(2).replace('.', ',');
  });
}

function removerItem(id) {
  fetch('<?= SITE_URL ?>/php/carrinho.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `acao=remover&produto_id=${id}`
  }).then(r => r.json()).then(d => {
    document.getElementById('item-' + id)?.remove();
    document.getElementById('total-resumo').textContent = 'R$ ' + parseFloat(d.total).toFixed(2).replace('.', ',');
    atualizarBadge(d.total_itens);
    if (d.total_itens == 0) location.reload();
  });
}
</script>

<?php require_once __DIR__ . '/php/footer_partial.php'; ?>
