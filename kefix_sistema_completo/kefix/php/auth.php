<?php
// ============================================
// KeFix - Autenticação (Login/Cadastro/Logout)
// ============================================
require_once __DIR__ . '/config.php';

$acao = $_GET['acao'] ?? '';

// ---------- LOGOUT ----------
if ($acao === 'sair') {
    session_destroy();
    redirecionar('/index.php');
}

// ---------- LOGIN ----------
if ($acao === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizar($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $_SESSION['erro_auth'] = 'Preencha e-mail e senha.';
        redirecionar('/php/auth.php?pagina=login');
    }

    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        if ($usuario['tipo'] === 'admin') {
            redirecionar('/admin/index.php');
        } else {
            redirecionar('/index.php');
        }
    } else {
        $_SESSION['erro_auth'] = 'E-mail ou senha incorretos.';
        redirecionar('/php/auth.php?pagina=login');
    }
}

// ---------- CADASTRO ----------
if ($acao === 'cadastrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = sanitizar($_POST['nome'] ?? '');
    $email    = sanitizar($_POST['email'] ?? '');
    $telefone = sanitizar($_POST['telefone'] ?? '');
    $senha    = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $_SESSION['erro_auth'] = 'Preencha todos os campos obrigatórios.';
        redirecionar('/php/auth.php?pagina=cadastro');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro_auth'] = 'E-mail inválido.';
        redirecionar('/php/auth.php?pagina=cadastro');
    }

    if (strlen($senha) < 6) {
        $_SESSION['erro_auth'] = 'A senha deve ter pelo menos 6 caracteres.';
        redirecionar('/php/auth.php?pagina=cadastro');
    }

    if ($senha !== $confirma) {
        $_SESSION['erro_auth'] = 'As senhas não coincidem.';
        redirecionar('/php/auth.php?pagina=cadastro');
    }

    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['erro_auth'] = 'Este e-mail já está cadastrado.';
        redirecionar('/php/auth.php?pagina=cadastro');
    }

    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $telefone, $hash]);

    $_SESSION['sucesso_auth'] = 'Cadastro realizado! Faça login para continuar.';
    redirecionar('/php/auth.php?pagina=login');
}

// ---------- EXIBIR FORMULÁRIOS ----------
$pagina = $_GET['pagina'] ?? 'login';
$erro   = $_SESSION['erro_auth'] ?? '';
$sucesso = $_SESSION['sucesso_auth'] ?? '';
unset($_SESSION['erro_auth'], $_SESSION['sucesso_auth']);

require_once __DIR__ . '/../php/header_partial.php';
?>

<main class="auth-main">
  <div class="auth-container">
    <div class="auth-logo">
      <img src="<?= SITE_URL ?>/images/logo.png" alt="KeFix" onerror="this.style.display='none'">
      <h1 class="auth-brand">Ke<span>Fix</span></h1>
      <p>Distribuidora de Peças</p>
    </div>

    <?php if ($erro): ?>
      <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
      <div class="alerta alerta-sucesso"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <?php if ($pagina === 'login'): ?>
    <!-- FORMULÁRIO DE LOGIN -->
    <div class="auth-card">
      <h2>Entrar na sua conta</h2>
      <form method="POST" action="<?= SITE_URL ?>/php/auth.php?acao=login">
        <div class="campo">
          <label>E-mail</label>
          <input type="email" name="email" placeholder="seu@email.com" required>
        </div>
        <div class="campo">
          <label>Senha</label>
          <input type="password" name="senha" placeholder="••••••" required>
        </div>
        <button type="submit" class="btn-primary btn-full">Entrar</button>
      </form>
      <p class="auth-link">Não tem conta? <a href="<?= SITE_URL ?>/php/auth.php?pagina=cadastro">Cadastre-se grátis</a></p>
    </div>

    <?php else: ?>
    <!-- FORMULÁRIO DE CADASTRO -->
    <div class="auth-card">
      <h2>Criar conta</h2>
      <form method="POST" action="<?= SITE_URL ?>/php/auth.php?acao=cadastrar">
        <div class="campo">
          <label>Nome completo *</label>
          <input type="text" name="nome" placeholder="Seu nome" required>
        </div>
        <div class="campo">
          <label>E-mail *</label>
          <input type="email" name="email" placeholder="seu@email.com" required>
        </div>
        <div class="campo">
          <label>Telefone</label>
          <input type="tel" name="telefone" placeholder="(44) 99999-9999">
        </div>
        <div class="campo">
          <label>Senha * (mín. 6 caracteres)</label>
          <input type="password" name="senha" placeholder="••••••" required minlength="6">
        </div>
        <div class="campo">
          <label>Confirmar senha *</label>
          <input type="password" name="confirma_senha" placeholder="••••••" required>
        </div>
        <button type="submit" class="btn-primary btn-full">Criar conta</button>
      </form>
      <p class="auth-link">Já tem conta? <a href="<?= SITE_URL ?>/php/auth.php?pagina=login">Entrar</a></p>
    </div>
    <?php endif; ?>
  </div>
</main>

<?php require_once __DIR__ . '/../php/footer_partial.php'; ?>
