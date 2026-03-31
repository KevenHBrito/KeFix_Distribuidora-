<?php
// ============================================
// KeFix - Página de Produto
// ============================================
require_once __DIR__ . '/php/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirecionar('/index.php');

$pdo = conectar();
$stmt = $pdo->prepare("
    SELECT p.*, c.nome AS categoria_nome, c.slug AS categoria_slug
    FROM produtos p
    JOIN categorias c ON p.categoria_id = c.id
    WHERE p.id = ? AND p.ativo = 1
");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) redirecionar('/index.php');

// Produtos relacionados
$rel = $pdo->prepare("
    SELECT * FROM produtos
    WHERE categoria_id = ? AND id != ? AND ativo = 1
    LIMIT 4
");
$rel->execute([$produto['categoria_id'], $id]);
$relacionados = $rel->fetchAll();

$page_title = htmlspecialchars($produto['nome']) . ' - KeFix';
require_once __DIR__ . '/php/header_partial.php';
?>

<main class="container" style="padding-top:2rem;padding-bottom:3rem">
  <!-- Breadcrumb -->
  <nav class="breadcrumb">
    <a href="<?= SITE_URL ?>">Início</a> /
    <a href="<?= SITE_URL ?>/categoria.php?slug=<?= $produto['categoria_slug'] ?>"><?= htmlspecialchars($produto['categoria_nome']) ?></a> /
    <span><?= htmlspecialchars($produto['nome']) ?></span>
  </nav>

  <!-- Produto -->
  <div class="produto-detalhe">
    <div class="produto-img-wrap">
      <img src="<?= SITE_URL ?>/images/produtos/<?= htmlspecialchars($produto['imagem']) ?>"
           alt="<?= htmlspecialchars($produto['nome']) ?>"
           onerror="this.src='<?= SITE_URL ?>/images/sem-imagem.png'"
           id="img-principal">
    </div>
    <div class="produto-info">
      <span class="produto-categoria"><?= htmlspecialchars($produto['categoria_nome']) ?></span>
      <h1><?= htmlspecialchars($produto['nome']) ?></h1>
      <div class="produto-preco"><?= formatar_preco($produto['preco']) ?></div>

      <?php if ($produto['estoque'] > 0): ?>
        <span class="badge-estoque em-estoque">✓ Em estoque (<?= $produto['estoque'] ?> unidades)</span>
      <?php else: ?>
        <span class="badge-estoque sem-estoque">✗ Produto esgotado</span>
      <?php endif; ?>

      <div class="produto-descricao">
        <h3>Descrição</h3>
        <p><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
      </div>

      <?php if ($produto['estoque'] > 0): ?>
      <div class="produto-acoes">
        <div class="qty-control">
          <button onclick="alterarQty(-1)">-</button>
          <input type="number" id="qty" value="1" min="1" max="<?= $produto['estoque'] ?>">
          <button onclick="alterarQty(1)">+</button>
        </div>
        <button class="btn-primary btn-carrinho-grande" onclick="adicionarCarrinhoQty(<?= $produto['id'] ?>)">
          <i data-lucide="shopping-cart"></i> Adicionar ao carrinho
        </button>
      </div>
      <?php endif; ?>

      <div class="produto-garantias">
        <span><i data-lucide="shield-check"></i> Garantia de qualidade</span>
        <span><i data-lucide="truck"></i> Frete para todo Brasil</span>
        <span><i data-lucide="refresh-ccw"></i> Troca em 7 dias</span>
      </div>
    </div>
  </div>

  <!-- Relacionados -->
  <?php if ($relacionados): ?>
  <section style="margin-top:3rem">
    <h2 class="secao-titulo">Produtos relacionados</h2>
    <div class="grid-produtos">
      <?php foreach ($relacionados as $r): ?>
      <div class="card-produto">
        <a href="<?= SITE_URL ?>/produto.php?id=<?= $r['id'] ?>" class="card-img">
          <img src="<?= SITE_URL ?>/images/produtos/<?= htmlspecialchars($r['imagem']) ?>"
               alt="<?= htmlspecialchars($r['nome']) ?>"
               onerror="this.src='<?= SITE_URL ?>/images/sem-imagem.png'">
        </a>
        <div class="card-info">
          <h3><a href="<?= SITE_URL ?>/produto.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['nome']) ?></a></h3>
          <div class="card-rodape">
            <strong class="card-preco"><?= formatar_preco($r['preco']) ?></strong>
            <button class="btn-add-carrinho" onclick="adicionarCarrinho(<?= $r['id'] ?>)">
              <i data-lucide="shopping-cart" style="width:16px;height:16px"></i>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</main>

<script>
function alterarQty(delta) {
  const input = document.getElementById('qty');
  const max = parseInt(input.max);
  let val = parseInt(input.value) + delta;
  if (val < 1) val = 1;
  if (val > max) val = max;
  input.value = val;
}

function adicionarCarrinhoQty(id) {
  const qty = document.getElementById('qty').value;
  adicionarCarrinho(id, parseInt(qty));
}
</script>

<?php require_once __DIR__ . '/php/footer_partial.php'; ?>
