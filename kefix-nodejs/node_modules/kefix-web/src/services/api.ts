import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:4000/api',
  timeout: 10000
});

export type Categoria = { id:number; nome:string; slug:string; icone:string; };
export type Produto = { id:number; nome:string; descricao:string; preco:number; estoque:number; imagem:string; destaque:number; categoria_nome:string; categoria_slug:string; };

export async function fetchCategorias() {
  const res = await api.get<Categoria[]>('/categories');
  return res.data;
}

export async function fetchProdutos(params?: { category?: string; search?: string;}) {
  const res = await api.get<Produto[]>('/products', { params });
  return res.data;
}

export async function fetchProduto(id: number) {
  const res = await api.get<Produto>(`/products/${id}`);
  return res.data;
}

export async function fetchDestaques() {
  const res = await api.get<Produto[]>('/featured');
  return res.data;
}

export async function login(email: string, senha: string) {
  const res = await api.post('/auth/login', { email, senha });
  return res.data;
}

export async function register(nome: string, email:string, senha:string, telefone?:string) {
  const res = await api.post('/auth/register', { nome, email, senha, telefone });
  return res.data;
}

export async function placeOrder(data:any) {
  const res = await api.post('/orders', data);
  return res.data;
}

export async function fetchOrders(userId:number) {
  const res = await api.get(`/orders/${userId}`);
  return res.data;
}

export default api;
