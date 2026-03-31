<?php
// ============================================
// KeFix Admin - Gerenciar Produtos
// ============================================
require_once __DIR__ . '/../php/config.php';
if (!admin_logado()) redirecionar('/php/auth.php?pagina=login');

$pdo = conectar();
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();

$acao = $_GET['acao'] ?? 'listar';
$erro = $_SESSION['admin_erro'] ?? '';
$sucesso = $_SESSION['admin_sucesso'] ?? '';
unset($_SESSION['admin_erro'], $_SESSION['admin_sucesso']);

// ---------- SALVAR PRODUTO ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $nome        = sanitizar($_POST['nome'] ?? '');
    $descricao   = sanitizar($_POST['descricao'] ?? '');
    $preco       = (float)str_replace(',', '.', $_POST['preco'] ?? 0);
    $estoque     = (int)($_POST['estoque'] ?? 0);
    $categoria   = (int)($_POST['categoria_id'] ?? 0);
    $destaque    = isset($_POST['destaque']) ? 1 : 0;

    if (empty($nome) || $preco <= 0 || $categoria <= 0) {
        $_SESSION['admin_erro'] = 'Preencha todos os campos obrigatórios.';
        redirecionar('/admin/produtos.php?acao=' . ($id ? 'editar&id='.$id : 'novo'));
    }

    // Upload de imagem
    $imagem = $_POST['imagem_atual'] ?? 'sem-imagem.png';
    if (!empty($_FILES['imagem']['name'])) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $permitidos)) {
            $_SESSION['admin_erro'] = 'Formato de imagem inválido. Use JPG, PNG ou WEBP.';
            redirecionar('/admin/produtos.php?acao=novo');
        }
        $nome_img = uniqid('produto_') . '.' . $ext;
        $destino = __DIR__ . '/../images/produtos/' . $nome_img;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
            $imagem = $nome_img;
        }
    }

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE produtos SET nome=?, descricao=?, preco=?, estoque=?, categoria_id=?, destaque=?, imagem=? WHERE id=?");
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria, $destaque, $imagem, $id]);
        $_SESSION['admin_sucesso'] = 'Produto atualizado com sucesso!';
    } else {
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, categoria_id, destaque, imagem) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria, $destaque, $imagem]);
        $_SESSION['admin_sucesso'] = 'Produto cadastrado com sucesso!';
    }
    redirecionar('/admin/produtos.php');
}

// ---------- DELETAR PRODUTO ----------
if ($acao === 'deletar') {
    $id = (int)($_GET['id'] ?? 0);
    $pdo->prepare("UPDATE produtos SET ativo = 0 WHERE id = ?")->execute([$id]);
    $_SESSION['admin_sucesso'] = 'Produto removido.';
    redirecionar('/admin/produtos.php');
}

// ---------- EDITAR: carregar produto ----------
$produto_edit = null;
if ($acao === 'editar') {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $produto_edit = $stmt->fetch();
}

// ---------- LISTAR ----------
$produtos = $pdo->query("
    SELECT p.*, c.nome AS categoria_nome
    FROM produtos p JOIN categorias c ON c.id = p.categoria_id
    WHERE p.ativo = 1 ORDER BY p.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="site-url" content="<?= SITE_URL ?>">
  <title>Produtos - Admin KeFix</title>
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
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-topbar">
      <h1>Produtos</h1>
      <?php if ($acao === 'listar'): ?>
      <a href="?acao=novo" class="btn-primary">+ Novo produto</a>
      <?php else: ?>
      <a href="?" class="btn-outline">← Voltar</a>
      <?php endif; ?>
    </div>

    <?php if ($erro): ?><div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="alerta alerta-sucesso"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>

    <?php if ($acao === 'novo' || $acao === 'editar'): ?>
    <!-- FORMULÁRIO -->
    <div class="admin-card">
      <h2><?= $acao === 'novo' ? 'Novo produto' : 'Editar produto' ?></h2>
      <form method="POST" enctype="multipart/form-data" class="form-admin">
        <?php if ($produto_edit): ?>
          <input type="hidden" name="id" value="<?= $produto_edit['id'] ?>">
          <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($produto_edit['imagem']) ?>">
        <?php endif; ?>
        <div class="form-row">
          <div class="campo">
            <label>Nome do produto *</label>
            <input type="text" name="nome" required value="<?= htmlspecialchars($produto_edit['nome'] ?? '') ?>">
          </div>
          <div class="campo">
            <label>Categoria *</label>
            <select name="categoria_id" required>
              <option value="">Selecione...</option>
              <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($produto_edit['categoria_id'] ?? '')==$c['id']?'selected':'' ?>>
                  <?= htmlspecialchars($c['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="campo">
            <label>Preço (R$) *</label>
            <input type="number" name="preco" step="0.01" min="0.01" required value="<?= $produto_edit['preco'] ?? '' ?>">
          </div>
          <div class="campo">
            <label>Estoque</label>
            <input type="number" name="estoque" min="0" value="<?= $produto_edit['estoque'] ?? 0 ?>">
          </div>
        </div>
        <div class="campo">
          <label>Descrição</label>
          <textarea name="descricao" rows="4"><?= htmlspecialchars($produto_edit['descricao'] ?? '') ?></textarea>
        </div>
        <div class="campo">
          <label>Imagem do produto</label>
          <?php if (!empty($produto_edit['imagem']) && $produto_edit['imagem'] !== 'sem-imagem.png'): ?>
            <img src="<?= SITE_URL ?>/images/produtos/<?= $produto_edit['imagem'] ?>" style="height:80px;margin-bottom:.5rem;border-radius:8px">
          <?php endif; ?>
          <input type="file" name="imagem" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="campo">
          <label class="checkbox-label">
            <input type="checkbox" name="destaque" value="1" <?= ($produto_edit['destaque'] ?? 0)?'checked':'' ?>>
            Destacar na página inicial
          </label>
        </div>
        <button type="submit" class="btn-primary">Salvar produto</button>
      </form>
    </div>

    <?php else: ?>
    <!-- LISTA -->
    <div class="admin-card">
      <div class="tabela-wrap">
        <table class="tabela">
          <thead>
            <tr><th>Img</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Destaque</th><th>Ações</th></tr>
          </thead>
          <tbody>
            <?php foreach ($produtos as $p): ?>
            <tr>
              <td><img src="<?= SITE_URL ?>/images/produtos/<?= htmlspecialchars($p['imagem']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:6px" onerror="this.src='<?= SITE_URL ?>/images/sem-imagem.png'"></td>
              <td><strong><?= htmlspecialchars($p['nome']) ?></strong></td>
              <td><?= htmlspecialchars($p['categoria_nome']) ?></td>
              <td><?= formatar_preco($p['preco']) ?></td>
              <td><?= $p['estoque'] ?></td>
              <td><?= $p['destaque'] ? '⭐' : '-' ?></td>
              <td class="acoes-tabela">
                <a href="?acao=editar&id=<?= $p['id'] ?>" class="btn-sm">✏️ Editar</a>
                <a href="?acao=deletar&id=<?= $p['id'] ?>" class="btn-sm btn-danger" onclick="return confirm('Remover produto?')">🗑️</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </main>
</div>
<script>lucide.createIcons();</script>
</body></html>
