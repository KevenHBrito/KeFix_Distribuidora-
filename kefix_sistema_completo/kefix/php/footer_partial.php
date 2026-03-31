<!-- Footer -->
<footer class="footer">
  <div class="container footer-grid">
    <div class="footer-col">
      <div class="footer-logo">
        <span class="logo-nome">KeFix</span>
        <small>Distribuidora</small>
      </div>
      <p>Peças originais e de qualidade para todos os modelos. Entrega rápida para todo o Brasil.</p>
    </div>
    <div class="footer-col">
      <h4>Categorias</h4>
      <ul>
        <li><a href="<?= SITE_URL ?>/categoria.php?slug=tela">Telas</a></li>
        <li><a href="<?= SITE_URL ?>/categoria.php?slug=bateria">Baterias</a></li>
        <li><a href="<?= SITE_URL ?>/categoria.php?slug=conector-carga">Conectores</a></li>
        <li><a href="<?= SITE_URL ?>/categoria.php?slug=camera">Câmeras</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Minha Conta</h4>
      <ul>
        <li><a href="<?= SITE_URL ?>/php/auth.php?pagina=login">Entrar</a></li>
        <li><a href="<?= SITE_URL ?>/php/auth.php?pagina=cadastro">Cadastrar</a></li>
        <li><a href="<?= SITE_URL ?>/minha-conta.php">Meus Pedidos</a></li>
        <li><a href="<?= SITE_URL ?>/carrinho.php">Carrinho</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Contato</h4>
      <p><i data-lucide="phone" style="width:14px;height:14px"></i> (44) 99999-9999</p>
      <p><i data-lucide="mail" style="width:14px;height:14px"></i> contato@kefix.com.br</p>
      <p><i data-lucide="map-pin" style="width:14px;height:14px"></i> Umuarama - PR</p>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?= date('Y') ?> KeFix Distribuidora. Todos os direitos reservados.</p>
    </div>
  </div>
</footer>

<script src="<?= SITE_URL ?>/js/main.js"></script>
<script>lucide.createIcons();</script>
</body>
</html>
