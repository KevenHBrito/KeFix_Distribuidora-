// ============================================
// KeFix - JavaScript Principal
// ============================================

const SITE_URL = document.querySelector('meta[name="site-url"]')?.content || '';

// ---------- CARRINHO ----------

/**
 * Adiciona produto ao carrinho via AJAX
 * @param {number} produtoId
 * @param {number} quantidade
 */
function adicionarCarrinho(produtoId, quantidade = 1) {
  const btn = document.querySelector(`[onclick*="adicionarCarrinho(${produtoId}"]`);
  if (btn) {
    btn.disabled = true;
    btn.style.opacity = '.6';
  }

  fetch(`${SITE_URL}/php/carrinho.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `acao=adicionar&produto_id=${produtoId}&quantidade=${quantidade}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.sucesso) {
      atualizarBadge(data.total_itens);
      mostrarToast(data.mensagem, 'sucesso');
    } else {
      mostrarToast(data.mensagem || 'Erro ao adicionar.', 'erro');
    }
  })
  .catch(() => mostrarToast('Erro de conexão.', 'erro'))
  .finally(() => {
    if (btn) {
      btn.disabled = false;
      btn.style.opacity = '1';
    }
  });
}

/**
 * Atualiza o badge do carrinho no header
 * @param {number} total
 */
function atualizarBadge(total) {
  const badge = document.getElementById('badge-carrinho');
  if (badge) {
    badge.textContent = total > 0 ? total : '';
  }
}

// ---------- TOAST ----------

/**
 * Exibe mensagem flutuante na tela
 * @param {string} mensagem
 * @param {string} tipo - 'sucesso' | 'erro' | 'info'
 */
function mostrarToast(mensagem, tipo = 'sucesso') {
  // Remover toast existente
  document.querySelector('.kefix-toast')?.remove();

  const toast = document.createElement('div');
  toast.className = `kefix-toast toast-${tipo}`;
  toast.textContent = mensagem;

  const cores = {
    sucesso: { bg: '#D1FAE5', cor: '#065F46', borda: '#A7F3D0' },
    erro:    { bg: '#FEE2E2', cor: '#991B1B', borda: '#FECACA' },
    info:    { bg: '#DBEAFE', cor: '#1E40AF', borda: '#BFDBFE' },
  };
  const c = cores[tipo] || cores.info;

  Object.assign(toast.style, {
    position: 'fixed',
    bottom: '2rem',
    right: '1.5rem',
    background: c.bg,
    color: c.cor,
    border: `1px solid ${c.borda}`,
    padding: '.85rem 1.4rem',
    borderRadius: '10px',
    fontFamily: "'Inter', sans-serif",
    fontSize: '.9rem',
    fontWeight: '500',
    zIndex: '9999',
    boxShadow: '0 4px 20px rgba(0,0,0,.12)',
    transform: 'translateY(20px)',
    opacity: '0',
    transition: 'all .3s ease',
    maxWidth: '320px',
  });

  document.body.appendChild(toast);

  // Animação de entrada
  requestAnimationFrame(() => {
    toast.style.transform = 'translateY(0)';
    toast.style.opacity = '1';
  });

  // Auto-remover
  setTimeout(() => {
    toast.style.transform = 'translateY(20px)';
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ---------- MENU MOBILE ----------

function toggleMenu() {
  const nav = document.getElementById('nav-menu');
  if (nav) nav.classList.toggle('aberto');
}

// Fechar menu ao clicar fora
document.addEventListener('click', function(e) {
  const nav = document.getElementById('nav-menu');
  const btn = document.querySelector('.btn-menu-mobile');
  if (nav && btn && !nav.contains(e.target) && !btn.contains(e.target)) {
    nav.classList.remove('aberto');
  }
});

// ---------- MÁSCARA DE TELEFONE ----------
document.addEventListener('DOMContentLoaded', () => {
  const telInputs = document.querySelectorAll('input[type="tel"]');
  telInputs.forEach(input => {
    input.addEventListener('input', function () {
      let v = this.value.replace(/\D/g, '').substring(0, 11);
      if (v.length > 10) {
        v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
      } else if (v.length > 6) {
        v = v.replace(/^(\d{2})(\d{4})(\d+)$/, '($1) $2-$3');
      } else if (v.length > 2) {
        v = v.replace(/^(\d{2})(\d+)$/, '($1) $2');
      }
      this.value = v;
    });
  });

  // Atualizar badge do carrinho ao carregar
  fetch(`${SITE_URL}/php/carrinho.php?acao=contar`)
    .then(r => r.json())
    .then(d => atualizarBadge(d.total_itens))
    .catch(() => {});
});
