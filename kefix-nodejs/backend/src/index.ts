import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import mysql from 'mysql2/promise';
import bcrypt from 'bcryptjs';

dotenv.config();

const app = express();
app.use(cors());
app.use(express.json());

const db = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASS || '',
  database: process.env.DB_NAME || 'kefix_db',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  dateStrings: true
});

app.get('/api/categories', async (req, res) => {
  const [rows] = await db.query('SELECT id, nome, slug, icone FROM categorias ORDER BY nome');
  res.json(rows);
});

app.get('/api/products', async (req, res) => {
  const { category, search } = req.query;
  let sql = `SELECT p.id, p.nome, p.descricao, p.preco, p.estoque, p.imagem, p.destaque, p.ativo, c.nome AS categoria_nome, c.slug AS categoria_slug FROM produtos p JOIN categorias c ON p.categoria_id = c.id WHERE p.ativo = 1`;
  const params: Array<string | number> = [];

  if (category) {
    sql += ' AND c.slug = ?';
    params.push(category as string);
  }
  if (search) {
    sql += ' AND p.nome LIKE ?';
    params.push(`%${search}%`);
  }
  const [rows] = await db.query(sql, params);
  res.json(rows);
});

app.get('/api/products/:id', async (req, res) => {
  const [rows] = await db.query('SELECT p.id, p.nome, p.descricao, p.preco, p.estoque, p.imagem, p.destaque, p.ativo, c.nome AS categoria_nome, c.slug AS categoria_slug FROM produtos p JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ? AND p.ativo = 1', [req.params.id]);
  const product = (rows as any[])[0];
  if (!product) return res.status(404).json({ error: 'Produto não encontrado' });
  res.json(product);
});

app.get('/api/featured', async (req, res) => {
  const [rows] = await db.query('SELECT p.id, p.nome, p.preco, p.estoque, p.imagem, c.nome AS categoria_nome FROM produtos p JOIN categorias c ON p.categoria_id = c.id WHERE p.destaque = 1 AND p.ativo = 1 ORDER BY p.id DESC LIMIT 8');
  res.json(rows);
});

app.post('/api/auth/register', async (req, res) => {
  const { nome, email, telefone, senha } = req.body;
  if (!nome || !email || !senha) return res.status(400).json({ error: 'Campos obrigatórios faltando' });

  const [exists] = await db.query('SELECT id FROM usuarios WHERE email = ?', [email]);
  if ((exists as any[]).length) return res.status(409).json({ error: 'E-mail já cadastrado' });

  const hash = await bcrypt.hash(senha, 10);
  await db.query('INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)', [nome, email, telefone || '', hash]);
  res.json({ success: true });
});

app.post('/api/auth/login', async (req, res) => {
  const { email, senha } = req.body;
  if (!email || !senha) return res.status(400).json({ error: 'E-mail e senha são obrigatórios' });

  const [rows] = await db.query('SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ? LIMIT 1', [email]);
  const user = (rows as any[])[0];
  if (!user) return res.status(401).json({ error: 'Credenciais inválidas' });

  const valid = await bcrypt.compare(senha, user.senha);
  if (!valid) return res.status(401).json({ error: 'Credenciais inválidas' });

  res.json({ id: user.id, nome: user.nome, email: user.email, tipo: user.tipo });
});

app.post('/api/orders', async (req, res) => {
  const { userId, nome_cliente, telefone, endereco, forma_pagamento, itens } = req.body;
  if (!nome_cliente || !endereco || !forma_pagamento || !Array.isArray(itens) || itens.length === 0) return res.status(400).json({ error: 'Dados de pedido inválidos' });

  const total = itens.reduce((sum: number, item: any) => sum + item.preco * item.quantidade, 0);

  const conn = await db.getConnection();
  try {
    await conn.beginTransaction();
    const [result] = await conn.query('INSERT INTO pedidos (usuario_id, nome_cliente, telefone, endereco, forma_pagamento, total, status) VALUES (?, ?, ?, ?, ?, ?, ?)', [userId || null, nome_cliente, telefone || '', endereco, forma_pagamento, total, 'pendente']);
    const pedidoId = (result as any).insertId;

    for (const item of itens) {
      await conn.query('INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)', [pedidoId, item.produto_id, item.quantidade, item.preco]);
      await conn.query('UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?', [item.quantidade, item.produto_id, item.quantidade]);
    }

    await conn.commit();
    res.json({ success: true, pedidoId });
  } catch (error) {
    await conn.rollback();
    console.error(error);
    res.status(500).json({ error: 'Erro interno ao criar pedido' });
  } finally {
    conn.release();
  }
});

app.get('/api/orders/:userId', async (req, res) => {
  const userId = Number(req.params.userId);
  if (!userId) return res.status(400).json({ error: 'User id inválido' });

  const [pedidos] = await db.query('SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY criado_em DESC', [userId]);
  res.json(pedidos);
});

app.listen(process.env.PORT || 4000, () => {
  console.log('API rodando na porta', process.env.PORT || 4000);
});
