// ============================================
// KeFix - JavaScript do Painel Admin
// ============================================

console.log('Admin.js carregado | window.SITE_URL:', window.SITE_URL);

/**
 * Atualiza o status de um pedido via AJAX
 * @param {number} pedidoId
 * @param {string} novoStatus
 */
function atualizarStatus(pedidoId, novoStatus) {
  // Construir a URL manualmente
  let baseUrl = window.SITE_URL;
  
  // Se window.SITE_URL não estiver definido, tentar recuperar do pathname
  if (!baseUrl || baseUrl.trim() === '') {
    const pathname = window.location.pathname;
    // Remover tudo após /admin/ para obter a URL base
    const adminIndex = pathname.indexOf('/admin/');
    if (adminIndex !== -1) {
      baseUrl = pathname.substring(0, adminIndex);
      // Pode precisar de protocol e host
      baseUrl = window.location.protocol + '//' + window.location.host + baseUrl;
    }
  }
  
  const url = baseUrl ? `${baseUrl}/admin/pedidos.php` : window.location.origin + '/admin/pedidos.php';
  console.log('[atualizarStatus] window.SITE_URL:', window.SITE_URL);
  console.log('[atualizarStatus] URL final:', url);
  
  fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `pedido_id=${pedidoId}&status=${novoStatus}`
  })
  .then(r => {
    if (!r.ok) {
      throw new Error(`HTTP ${r.status}: ${r.statusText}`);
    }
    return r.json();
  })
  .then(data => {
    if (data.sucesso) {
      mostrarToastAdmin(`Status atualizado para "${novoStatus}"`, 'sucesso');
    } else {
      mostrarToastAdmin(data.mensagem || 'Erro ao atualizar status.', 'erro');
    }
  })
  .catch(err => {
    console.error('Erro ao atualizar status:', err);
    mostrarToastAdmin('Erro de conexão: ' + err.message, 'erro');
  });
}

/**
 * Toast para o admin
 */
function mostrarToastAdmin(mensagem, tipo = 'sucesso') {
  document.querySelector('.admin-toast')?.remove();

  const toast = document.createElement('div');
  toast.className = 'admin-toast';
  toast.textContent = mensagem;

  const bg = tipo === 'sucesso' ? '#D1FAE5' : '#FEE2E2';
  const cor = tipo === 'sucesso' ? '#065F46' : '#991B1B';

  Object.assign(toast.style, {
    position: 'fixed',
    bottom: '1.5rem',
    right: '1.5rem',
    background: bg,
    color: cor,
    padding: '.75rem 1.2rem',
    borderRadius: '8px',
    fontFamily: "'Inter', sans-serif",
    fontSize: '.88rem',
    fontWeight: '500',
    zIndex: '9999',
    boxShadow: '0 4px 16px rgba(0,0,0,.12)',
    opacity: '0',
    transform: 'translateY(10px)',
    transition: 'all .25s',
  });

  document.body.appendChild(toast);
  requestAnimationFrame(() => {
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
  });

  setTimeout(() => {
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 250);
  }, 2500);
}

// Preview de imagem antes do upload
document.addEventListener('DOMContentLoaded', () => {
  const inputImg = document.querySelector('input[name="imagem"]');
  if (inputImg) {
    inputImg.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => {
        let preview = document.getElementById('preview-img');
        if (!preview) {
          preview = document.createElement('img');
          preview.id = 'preview-img';
          Object.assign(preview.style, {
            height: '80px',
            marginTop: '.5rem',
            borderRadius: '8px',
            display: 'block',
          });
          this.parentNode.insertBefore(preview, this.nextSibling);
        }
        preview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  }
});
