<?php
// ============================================
// KeFix - Gerenciamento do Carrinho (Sessão)
// ============================================
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// Inicializar carrinho na sessão
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

switch ($acao) {

    // ---------- ADICIONAR AO CARRINHO ----------
    case 'adicionar':
        $produto_id = (int)($_POST['produto_id'] ?? 0);
        $quantidade = (int)($_POST['quantidade'] ?? 1);

        if ($produto_id <= 0 || $quantidade <= 0) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
            exit;
        }

        $pdo = conectar();
        $stmt = $pdo->prepare("SELECT id, nome, preco, estoque, imagem FROM produtos WHERE id = ? AND ativo = 1");
        $stmt->execute([$produto_id]);
        $produto = $stmt->fetch();

        if (!$produto) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Produto não encontrado.']);
            exit;
        }

        $qtd_atual = $_SESSION['carrinho'][$produto_id]['quantidade'] ?? 0;
        $nova_qtd  = $qtd_atual + $quantidade;

        if ($nova_qtd > $produto['estoque']) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Estoque insuficiente.']);
            exit;
        }

        $_SESSION['carrinho'][$produto_id] = [
            'produto_id' => $produto['id'],
            'nome'       => $produto['nome'],
            'preco'      => $produto['preco'],
            'imagem'     => $produto['imagem'],
            'quantidade' => $nova_qtd,
        ];

        echo json_encode([
            'sucesso'   => true,
            'mensagem'  => 'Produto adicionado ao carrinho!',
            'total_itens' => array_sum(array_column($_SESSION['carrinho'], 'quantidade'))
        ]);
        break;

    // ---------- ATUALIZAR QUANTIDADE ----------
    case 'atualizar':
        $produto_id = (int)($_POST['produto_id'] ?? 0);
        $quantidade = (int)($_POST['quantidade'] ?? 1);

        if (isset($_SESSION['carrinho'][$produto_id])) {
            if ($quantidade <= 0) {
                unset($_SESSION['carrinho'][$produto_id]);
            } else {
                $_SESSION['carrinho'][$produto_id]['quantidade'] = $quantidade;
            }
        }
        echo json_encode(['sucesso' => true, 'total' => calcular_total()]);
        break;

    // ---------- REMOVER ITEM ----------
    case 'remover':
        $produto_id = (int)($_POST['produto_id'] ?? 0);
        unset($_SESSION['carrinho'][$produto_id]);
        echo json_encode([
            'sucesso' => true,
            'total'   => calcular_total(),
            'total_itens' => array_sum(array_column($_SESSION['carrinho'], 'quantidade'))
        ]);
        break;

    // ---------- CONTAR ITENS ----------
    case 'contar':
        $total = array_sum(array_column($_SESSION['carrinho'], 'quantidade'));
        echo json_encode(['total_itens' => $total]);
        break;

    default:
        echo json_encode(['erro' => 'Ação inválida.']);
}

function calcular_total() {
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    return number_format($total, 2, '.', '');
}
?>
