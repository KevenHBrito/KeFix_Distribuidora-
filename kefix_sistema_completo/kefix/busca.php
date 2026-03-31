<?php
// Redireciona para categoria.php com parâmetro de busca
require_once __DIR__ . '/php/config.php';
$q = sanitizar($_GET['q'] ?? '');
header("Location: " . SITE_URL . "/categoria.php?q=" . urlencode($q));
exit;
