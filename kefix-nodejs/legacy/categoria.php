<?php
// ============================================
// KeFix - Página de Categoria
// ============================================
require_once __DIR__ . '/php/config.php';
$pdo = conectar();

$slug = sanitizar($_GET['slug'] ?? '');
$busca = sanitizar($_GET['q'] ?? '');

$categoria = null;
if ($slug) {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE slug = ?");
    $stmt->execute([$slug]);
    $categoria = $stmt->fetch();
}

// Buscar produtos
if ($categoria) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome AS categoria_nome FROM produtos p
        JOIN categorias c ON p.categoria_id = c.id
        WHERE p.categoria_id = ? AND p.ativo = 1
        ORDER BY p.destaque DESC, p.nome ASC
    ");
    $stmt->execute([$categoria['id']]);
} elseif ($busca) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome AS categoria_nome FROM produtos p
        JOIN categorias c ON p.categoria_id = c.id
        WHERE (p.nome LIKE ? OR p.descricao LIKE ?) AND p.ativo = 1
        ORDER BY p.nome ASC
    ");
    $like = "%$busca%";
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, c.nome AS categoria_nome FROM produtos p
        JOIN categorias c ON p.categoria_id = c.id
        WHERE p.ativo = 1
        ORDER BY p.destaque DESC, p.nome ASC
    ");
}

$produtos = $stmt->fetchAll();
$page_title = ($categoria ? $categoria['nome'] : ($busca ? "Busca: $busca" : 'Todos os Produtos')) . ' - KeFix';
require_once __DIR__ . '/php/header_partial.php';
?>

<main class="container" style="padding-top:2rem;padding-bottom:3rem">
  <div class="secao-header">
    <h1 class="secao-titulo">
      <?= $categoria ? htmlspecialchars($categoria['nome']) : ($busca ? "Resultados para: \"$busca\"" : 'Todos os Produtos') ?>
    </h1>
    <span><?= count($produtos) ?> produto(s) encontrado(s)</span>
  </div>

  <?php if (empty($produtos)): ?>
    <div class="vazio">
      <i data-lucide="package-x" style="width:64px;height:64px;color:#ccc"></i>
      <p>Nenhum produto encontrado.</p>
      <a href="<?= SITE_URL ?>/index.php" class="btn-primary">Ver todos</a>
    </div>
  <?php else: ?>
  <div class="grid-produtos">
    <?php foreach ($produtos as $p): ?>
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
  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/php/footer_partial.php'; ?>
