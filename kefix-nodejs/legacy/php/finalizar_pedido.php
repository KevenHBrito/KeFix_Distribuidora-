<?php
// ============================================
// KeFix - Checkout / Finalizar Pedido
// ============================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validações
    $nome     = sanitizar($_POST['nome'] ?? '');
    $telefone = sanitizar($_POST['telefone'] ?? '');
    $rua      = sanitizar($_POST['rua'] ?? '');
    $numero   = sanitizar($_POST['numero'] ?? '');
    $bairro   = sanitizar($_POST['bairro'] ?? '');
    $cidade   = sanitizar($_POST['cidade'] ?? '');
    $estado   = sanitizar($_POST['estado'] ?? '');
    $pagamento = sanitizar($_POST['pagamento'] ?? '');
    $obs      = sanitizar($_POST['observacoes'] ?? '');

    $formas_validas = ['pix', 'cartao', 'dinheiro'];

    // Montar endereço completo
    $endereco = "$rua, $numero - $bairro, $cidade - $estado";

    if (empty($nome) || empty($rua) || empty($numero) || empty($bairro) || empty($cidade) || empty($estado) || !in_array($pagamento, $formas_validas)) {
        $_SESSION['erro_checkout'] = 'Preencha todos os campos obrigatórios.';
        redirecionar('/carrinho.php');
    }
    // Salvar dados para próxima compra na sessão
    $_SESSION['dados_pedido'] = [
        'nome' => $nome,
        'telefone' => $telefone,
        'rua' => $rua,
        'numero' => $numero,
        'bairro' => $bairro,
        'cidade' => $cidade,
        'estado' => $estado
    ];
    if (empty($_SESSION['carrinho'])) {
        $_SESSION['erro_checkout'] = 'Seu carrinho está vazio.';
        redirecionar('/index.php');
    }

    // Calcular total
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }

    $pdo = conectar();

    try {
        $pdo->beginTransaction();

        // Inserir pedido
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $stmt = $pdo->prepare("
            INSERT INTO pedidos (usuario_id, nome_cliente, telefone, endereco, forma_pagamento, total, observacoes)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$usuario_id, $nome, $telefone, $endereco, $pagamento, $total, $obs]);
        $pedido_id = $pdo->lastInsertId();

        // Inserir itens
        $stmt_item = $pdo->prepare("
            INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($_SESSION['carrinho'] as $item) {
            $stmt_item->execute([
                $pedido_id,
                $item['produto_id'],
                $item['quantidade'],
                $item['preco']
            ]);
            // Atualizar estoque
            $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?")
                ->execute([$item['quantidade'], $item['produto_id']]);
        }

        $pdo->commit();

        // Se usuário está logado, salvar dados no banco também
        if ($usuario_id) {
            $pdo->prepare("
                UPDATE usuarios 
                SET telefone = ?, endereco = ?
                WHERE id = ?
            ")->execute([$telefone, $endereco, $usuario_id]);
        }

        // Limpar carrinho
        $_SESSION['carrinho'] = [];
        $_SESSION['pedido_confirmado'] = $pedido_id;

        redirecionar('/confirmacao.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['erro_checkout'] = 'Erro ao processar pedido. Tente novamente.';
        redirecionar('/carrinho.php');
    }
}
redirecionar('/index.php');
?>
