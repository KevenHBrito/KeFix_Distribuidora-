<?php
// ============================================
// KeFix Admin - Categorias
// ============================================
require_once __DIR__ . '/../php/config.php';
if (!admin_logado()) redirecionar('/php/auth.php?pagina=login');

$pdo = conectar();
$acao = $_GET['acao'] ?? 'listar';

$erro = $_SESSION['admin_erro'] ?? '';
$sucesso = $_SESSION['admin_sucesso'] ?? '';
unset($_SESSION['admin_erro'], $_SESSION['admin_sucesso']);

// Salvar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id'] ?? 0);
    $nome = sanitizar($_POST['nome'] ?? '');
    $icone = sanitizar($_POST['icone'] ?? 'box');

    if (empty($nome)) {
        $_SESSION['admin_erro'] = 'Nome obrigatório.';
        redirecionar('/admin/categorias.php?acao=' . ($id ? 'editar&id='.$id : 'novo'));
    }

    $slug = gerar_slug($nome);

    if ($id > 0) {
        $pdo->prepare("UPDATE categorias SET nome=?, slug=?, icone=? WHERE id=?")->execute([$nome, $slug, $icone, $id]);
        $_SESSION['admin_sucesso'] = 'Categoria atualizada!';
    } else {
        $pdo->prepare("INSERT INTO categorias (nome, slug, icone) VALUES (?,?,?)")->execute([$nome, $slug, $icone]);
        $_SESSION['admin_sucesso'] = 'Categoria criada!';
    }
    redirecionar('/admin/categorias.php');
}

// Deletar
if ($acao === 'deletar') {
    $id = (int)($_GET['id'] ?? 0);
    // Verificar se tem produtos
    $count = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE categoria_id = ? AND ativo = 1");
    $count->execute([$id]);
    if ($count->fetchColumn() > 0) {
        $_SESSION['admin_erro'] = 'Não é possível excluir: existem produtos nessa categoria.';
    } else {
        $pdo->prepare("DELETE FROM categorias WHERE id = ?")->execute([$id]);
        $_SESSION['admin_sucesso'] = 'Categoria removida.';
    }
    redirecionar('/admin/categorias.php');
}

$cat_edit = null;
if ($acao === 'editar') {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $cat_edit = $stmt->fetch();
}

$categorias = $pdo->query("SELECT c.*, COUNT(p.id) AS total_produtos FROM categorias c LEFT JOIN produtos p ON p.categoria_id = c.id AND p.ativo=1 GROUP BY c.id ORDER BY c.nome")->fetchAll();

$icones_disponiveis = ['monitor','battery-charging','zap','volume-2','mic','smartphone','layers','camera','box','cpu','tool','wifi'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="site-url" content="<?= SITE_URL ?>">
  <title>Categorias - Admin KeFix</title>
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
      <h1>Categorias</h1>
      <?php if ($acao === 'listar'): ?>
        <a href="?acao=novo" class="btn-primary">+ Nova categoria</a>
      <?php else: ?>
        <a href="?" class="btn-outline">← Voltar</a>
      <?php endif; ?>
    </div>

    <?php if ($erro): ?><div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="alerta alerta-sucesso"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>

    <?php if ($acao === 'novo' || $acao === 'editar'): ?>
    <div class="admin-card">
      <h2><?= $acao === 'novo' ? 'Nova categoria' : 'Editar categoria' ?></h2>
      <form method="POST" class="form-admin">
        <?php if ($cat_edit): ?>
          <input type="hidden" name="id" value="<?= $cat_edit['id'] ?>">
        <?php endif; ?>
        <div class="campo">
          <label>Nome da categoria *</label>
          <input type="text" name="nome" required value="<?= htmlspecialchars($cat_edit['nome'] ?? '') ?>">
        </div>
        <div class="campo">
          <label>Ícone (Lucide)</label>
          <select name="icone">
            <?php foreach ($icones_disponiveis as $ic): ?>
              <option value="<?= $ic ?>" <?= ($cat_edit['icone'] ?? 'box') === $ic ? 'selected' : '' ?>><?= $ic ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn-primary">Salvar</button>
      </form>
    </div>
    <?php else: ?>
    <div class="admin-card">
      <div class="tabela-wrap">
        <table class="tabela">
          <thead><tr><th>Ícone</th><th>Nome</th><th>Slug</th><th>Produtos</th><th>Ações</th></tr></thead>
          <tbody>
            <?php foreach ($categorias as $c): ?>
            <tr>
              <td><i data-lucide="<?= htmlspecialchars($c['icone']) ?>" style="width:20px;height:20px;color:#007BFF"></i></td>
              <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
              <td><code style="font-size:.78rem;background:#f1f5f9;padding:.2rem .4rem;border-radius:4px"><?= htmlspecialchars($c['slug']) ?></code></td>
              <td><?= $c['total_produtos'] ?></td>
              <td class="acoes-tabela">
                <a href="?acao=editar&id=<?= $c['id'] ?>" class="btn-sm">✏️ Editar</a>
                <a href="?acao=deletar&id=<?= $c['id'] ?>" class="btn-sm btn-danger" onclick="return confirm('Remover categoria?')">🗑️</a>
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
