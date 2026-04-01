export type CartItem = {
  produto_id: number;
  nome: string;
  preco: number;
  imagem: string;
  quantidade: number;
};

const STORAGE_KEY = 'kefix_cart';

export function loadCart(): CartItem[] {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

export function saveCart(items: CartItem[]) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
}

export function getCart(): CartItem[] {
  return loadCart();
}

export function countCartItems() {
  return loadCart().reduce((sum, item) => sum + item.quantidade, 0);
}

export function addToCart(item: CartItem) {
  const cart = loadCart();
  const existing = cart.find((i) => i.produto_id === item.produto_id);
  if (existing) {
    existing.quantidade += item.quantidade;
  } else {
    cart.push({ ...item });
  }
  saveCart(cart);
}

export function updateCart(produto_id: number, quantidade: number) {
  const cart = loadCart().map((item) =>
    item.produto_id === produto_id ? { ...item, quantidade } : item
  ).filter((item) => item.quantidade > 0);
  saveCart(cart);
}

export function clearCart() {
  saveCart([]);
}
