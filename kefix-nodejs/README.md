# KeFix Node.js - Sistema de E-commerce

Migração completa do sistema PHP para Node.js + React + TypeScript, mantendo a mesma estilização e funcionalidades.

## 🚀 Instalação Rápida (Windows)

Execute o script de instalação automática:

```bash
# Na raiz do projeto
install.bat
```

Este script irá:
- Verificar se Node.js está instalado
- Instalar todas as dependências automaticamente
- Mostrar próximos passos

## 🚀 Instalação Manual

### 1. Instalar Node.js
Baixe e instale o Node.js (versão 18+ recomendada):
- https://nodejs.org/

### 2. Instalar Dependências
```bash
# Na raiz do projeto (kefix-nodejs)
npm install

# Ou se preferir yarn
yarn install
```

### 3. Configurar Banco de Dados
- Certifique-se de que o MySQL está rodando (XAMPP ou similar)
- Importe o `database.sql` do sistema original para criar a base `kefix_db`
- Verifique as configurações em `backend/.env`

### 4. Rodar o Sistema
```bash
# Na raiz do projeto
npm run dev

# Isso iniciará:
# - Backend API em http://localhost:4000
# - Frontend React em http://localhost:5173
```

### 5. Acessar
- **Frontend:** http://localhost:5173
- **API:** http://localhost:4000/api

## 📁 Estrutura do Projeto

```
kefix-nodejs/
├── backend/                 # API Node.js + Express
│   ├── src/
│   │   ├── index.ts        # Servidor principal
│   │   └── ...
│   ├── package.json
│   ├── tsconfig.json
│   └── .env                # Configurações BD
├── frontend/                # React + TypeScript
│   ├── src/
│   │   ├── components/     # Componentes reutilizáveis
│   │   ├── pages/          # Páginas da aplicação
│   │   ├── services/       # Chamadas para API
│   │   ├── store/          # Estado do carrinho
│   │   ├── App.tsx         # Roteamento
│   │   ├── main.tsx        # Entrada da aplicação
│   │   └── index.css       # Estilos (copiados do PHP)
│   ├── public/             # Arquivos estáticos
│   │   ├── images/         # Imagens dos produtos
│   │   └── index.html
│   ├── package.json
│   ├── tsconfig.json
│   └── vite.config.ts
├── legacy/                 # Sistema PHP original (backup)
├── install.bat             # Script de instalação (Windows)
└── package.json            # Scripts para rodar tudo
```

## 🔧 Funcionalidades Mantidas

### Frontend (React)
- ✅ Página inicial com banner e produtos em destaque
- ✅ Listagem por categoria
- ✅ Busca de produtos
- ✅ Página de produto com galeria
- ✅ Carrinho com alteração de quantidade e remoção
- ✅ Checkout com formulário
- ✅ Confirmação de pedido
- ✅ Cadastro e login de usuários
- ✅ Área "Minha Conta" com histórico
- ✅ Design responsivo (igual ao PHP)

### Backend (Node.js)
- ✅ API REST para produtos, categorias, usuários
- ✅ Autenticação com JWT
- ✅ Gerenciamento de carrinho (localStorage)
- ✅ Processamento de pedidos
- ✅ Integração com MySQL (mesmo BD do PHP)

## 🛠️ Tecnologias Utilizadas

| Tecnologia | Versão | Uso |
|------------|--------|-----|
| Node.js | 18+ | Runtime backend |
| Express | 4.18+ | Framework web |
| React | 18.3+ | Frontend |
| TypeScript | 5.4+ | Tipagem |
| MySQL2 | 3.5+ | Banco de dados |
| Axios | 1.5+ | HTTP client |
| Lucide React | 0.375+ | Ícones |
| Vite | 5.4+ | Build tool |

## 📦 Scripts Disponíveis

```bash
# Desenvolvimento
npm run dev          # Roda backend + frontend simultaneamente

# Produção
npm run build        # Build do frontend
npm run start        # Roda apenas backend
```

## 🔒 Segurança
- ✅ Prepared Statements no backend
- ✅ Validação de entrada
- ✅ Hash de senhas com bcrypt
- ✅ CORS configurado

## 🎨 Estilização
- Mantida idêntica ao sistema PHP original
- CSS customizado com variáveis
- Responsivo para mobile e desktop
- Fontes Google: Inter + Rajdhani

---

**Nota:** Este é um sistema completo de e-commerce, migrado de PHP para Node.js/React, mantendo 100% das funcionalidades e visual do original.