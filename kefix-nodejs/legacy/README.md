# KeFix Distribuidora - Sistema de E-commerce
## Guia Completo de Instalação e Uso

---

## 📁 Estrutura do Projeto

```
kefix/
├── index.php              ← Página inicial
├── produto.php            ← Página de produto
├── categoria.php          ← Listagem por categoria / busca
├── carrinho.php           ← Carrinho + checkout
├── confirmacao.php        ← Confirmação de pedido
├── minha-conta.php        ← Área do cliente
├── busca.php              ← Redirect de busca
├── database.sql           ← Script do banco de dados
├── .htaccess              ← Configurações Apache
│
├── css/
│   ├── style.css          ← Estilos da loja
│   └── admin.css          ← Estilos do painel admin
│
├── js/
│   ├── main.js            ← JavaScript da loja
│   └── admin.js           ← JavaScript do admin
│
├── images/
│   ├── sem-imagem.png     ← Imagem padrão (criar manualmente)
│   └── produtos/          ← Imagens dos produtos (criar pasta)
│
├── php/
│   ├── config.php         ← ⚙️ CONFIGURAR AQUI: BD + URL
│   ├── auth.php           ← Login / Cadastro / Logout
│   ├── carrinho.php       ← API do carrinho (AJAX)
│   ├── finalizar_pedido.php ← Processa o checkout
│   ├── header_partial.php ← Header reutilizável
│   └── footer_partial.php ← Footer reutilizável
│
└── admin/
    ├── index.php          ← Dashboard admin
    ├── produtos.php       ← CRUD de produtos
    ├── pedidos.php        ← Gerenciar pedidos
    ├── categorias.php     ← Gerenciar categorias
    └── sidebar.php        ← Menu lateral admin
```

---

## 🚀 Como Rodar Localmente (XAMPP)

### 1. Instalar o XAMPP
Baixe em: https://www.apachefriends.org/
Versão recomendada: PHP 8.0 ou superior

### 2. Copiar os arquivos
Copie a pasta `kefix/` para:
```
C:\xampp\htdocs\kefix\        (Windows)
/opt/lampp/htdocs/kefix/      (Linux)
/Applications/XAMPP/htdocs/kefix/  (Mac)
```

### 3. Criar a pasta de imagens
Crie manualmente a pasta:
```
kefix/images/produtos/
```
E coloque uma imagem chamada `sem-imagem.png` em `kefix/images/`

### 4. Iniciar os serviços
No painel do XAMPP, inicie:
- ✅ Apache
- ✅ MySQL

---

## 🗄️ Configurar o Banco de Dados

### Opção A - Via phpMyAdmin (mais fácil)
1. Acesse: http://localhost/phpmyadmin
2. Clique em **"Novo"** (ou "New") no menu esquerdo
3. Crie um banco chamado `kefix_db`
4. Selecione `kefix_db` e clique na aba **SQL**
5. Cole o conteúdo do arquivo `database.sql`
6. Clique em **Executar**

### Opção B - Via terminal
```bash
mysql -u root -p < C:\xampp\htdocs\kefix\database.sql
```

---

## ⚙️ Configurar o Arquivo Principal

Abra o arquivo `php/config.php` e edite:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Seu usuário MySQL
define('DB_PASS', '');            // Sua senha MySQL (vazio no XAMPP padrão)
define('DB_NAME', 'kefix_db');
define('SITE_URL', 'http://localhost/kefix');  // URL do projeto
```

---

## 🌐 Acessar o Site

| Página              | URL                                      |
|---------------------|------------------------------------------|
| Página inicial      | http://localhost/kefix/                  |
| Login/Cadastro      | http://localhost/kefix/php/auth.php      |
| Carrinho            | http://localhost/kefix/carrinho.php      |
| **Painel Admin**    | http://localhost/kefix/admin/            |

---

## 🔐 Acesso ao Painel Administrativo

**URL:** http://localhost/kefix/admin/


**http://localhost/Projeto_Final/kefix_sistema_completo/kefix/**

**http://localhost/Projeto_Final/kefix_sistema_completo/kefix/admin/**


**Credenciais padrão:**
```
E-mail: admin@kefix.com.br
Senha:  Kev070920
```

> ⚠️ **IMPORTANTE:** Altere a senha do admin após o primeiro acesso!
> No phpMyAdmin, execute:
> ```sql
> UPDATE usuarios SET senha = 'NOVO_HASH' WHERE email = 'admin@kefix.com.br';
> ```
> Use um gerador de hash bcrypt para criar o novo hash.

---

## 🔧 Funcionalidades do Sistema

### Loja (Frontend)
- ✅ Página inicial com banner e produtos em destaque
- ✅ Listagem por categoria
- ✅ Busca de produtos
- ✅ Página de produto com galeria e "adicionar ao carrinho"
- ✅ Carrinho com alteração de quantidade e remoção
- ✅ Checkout com nome, endereço, telefone
- ✅ Formas de pagamento: PIX, Cartão, Dinheiro na retirada
- ✅ Confirmação de pedido com número
- ✅ Cadastro e login de clientes
- ✅ Área "Minha Conta" com histórico de pedidos
- ✅ Design responsivo (mobile + desktop)

### Painel Admin
- ✅ Dashboard com estatísticas
- ✅ CRUD completo de produtos (com upload de imagem)
- ✅ Gerenciar categorias
- ✅ Visualizar e atualizar status de pedidos
- ✅ Filtro de pedidos por status

---

## 🔒 Segurança Implementada

- ✅ Prepared Statements em todas as queries (proteção SQL Injection)
- ✅ `password_hash()` + `password_verify()` para senhas
- ✅ `htmlspecialchars()` em todos os outputs (proteção XSS)
- ✅ Validação de formulários no servidor
- ✅ Verificação de sessão admin em todas as páginas do painel
- ✅ Validação de tipo de arquivo no upload de imagens

---

## 🛠️ Personalização

### Trocar cores
Edite as variáveis no início do arquivo `css/style.css`:
```css
:root {
  --azul:        #007BFF;  /* Cor principal */
  --azul-escuro: #0056CC;  /* Hover/sombra */
  --azul-claro:  #E8F2FF;  /* Fundo suave */
}
```

### Adicionar nova categoria
1. Acesse o Admin → Categorias → Nova categoria
2. Ou insira diretamente no banco:
```sql
INSERT INTO categorias (nome, slug, icone) VALUES ('Nome', 'slug', 'icone-lucide');
```
Lista de ícones disponíveis em: https://lucide.dev/icons/

### URL do Frete Grátis
Edite em `php/header_partial.php` (barra de anúncio) e em `carrinho.php`:
```php
// Altere o valor 299 pelo valor desejado
$total >= 299
```

---

## 📦 Tecnologias Utilizadas

| Tecnologia | Versão | Uso |
|------------|--------|-----|
| PHP | 8.0+ | Backend / lógica |
| MySQL | 5.7+ | Banco de dados |
| HTML5 | - | Estrutura |
| CSS3 | - | Estilos |
| JavaScript | ES6+ | Interatividade |
| PDO | - | Acesso ao banco |
| Lucide Icons | Latest | Ícones |
| Google Fonts | - | Rajdhani + Inter |

---

## ❓ Dúvidas Frequentes

**O site não carrega as imagens dos produtos?**
Crie a pasta `images/produtos/` e adicione uma imagem `sem-imagem.png` em `images/`.

**Erro "Access denied for user root"?**
Verifique a senha no `php/config.php`. No XAMPP padrão a senha é vazia `''`.

**Erro "No such file or directory" no config.php?**
Verifique se o projeto está em `htdocs/kefix/` e se o `SITE_URL` está correto.

**Página em branco no admin?**
Certifique-se de estar logado com conta do tipo `admin`. Use admin@kefix.com.br / admin123.
