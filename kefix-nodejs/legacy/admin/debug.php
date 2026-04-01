<?php
require_once __DIR__ . '/php/config.php';
if (!admin_logado()) redirecionar('/php/auth.php?pagina=login');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Admin</title>
    <script>
        window.SITE_URL = '<?= SITE_URL ?>';
        console.log('SITE_URL definido:', window.SITE_URL);
    </script>
</head>
<body>
    <h1>Debug - Verificação de window.SITE_URL</h1>
    <p><strong>PHP SITE_URL:</strong> <code><?= SITE_URL ?></code></p>
    <button onclick="testarURL()">Testar URL</button>
    
    <script>
        function testarURL() {
            console.log('window.SITE_URL em testarURL():', window.SITE_URL);
            
            const url = window.SITE_URL ? `${window.SITE_URL}/admin/pedidos.php` : '/admin/pedidos.php';
            console.log('URL construída:', url);
            
            document.body.innerHTML += '<p>URL construída: <code>' + url + '</code></p>';
        }
    </script>
</body>
</html>
