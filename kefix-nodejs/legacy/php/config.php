<?php
// ============================================
// KeFix - Configuração do Banco de Dados
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Altere para seu usuário MySQL
define('DB_PASS', '');            // Altere para sua senha MySQL
define('DB_NAME', 'kefix_db');
define('SITE_URL', 'http://localhost/Projeto_Final/kefix_sistema_completo/kefix');
define('SITE_NAME', 'KeFix Distribuidora');

// Conexão PDO com proteção contra SQL Injection
function conectar() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die(json_encode(['erro' => 'Erro de conexão com o banco de dados.']));
        }
    }
    return $pdo;
}

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Funções auxiliares
function usuario_logado() {
    return isset($_SESSION['usuario_id']);
}

function admin_logado() {
    return isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] === 'admin';
}

function redirecionar($url) {
    header("Location: " . SITE_URL . $url);
    exit;
}

function sanitizar($valor) {
    return htmlspecialchars(strip_tags(trim($valor)));
}

function formatar_preco($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function gerar_slug($texto) {
    $texto = strtolower($texto);
    $texto = preg_replace('/[áàãâä]/u', 'a', $texto);
    $texto = preg_replace('/[éèêë]/u', 'e', $texto);
    $texto = preg_replace('/[íìîï]/u', 'i', $texto);
    $texto = preg_replace('/[óòõôö]/u', 'o', $texto);
    $texto = preg_replace('/[úùûü]/u', 'u', $texto);
    $texto = preg_replace('/[ç]/u', 'c', $texto);
    $texto = preg_replace('/[^a-z0-9\s-]/', '', $texto);
    $texto = preg_replace('/[\s-]+/', '-', $texto);
    return trim($texto, '-');
}
?>
