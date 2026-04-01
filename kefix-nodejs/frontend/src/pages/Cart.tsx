import { Link, useNavigate } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { getCart, updateCart, clearCart, CartItem } from '../store/cart';

export default function Cart() {
  const [items, setItems] = useState<CartItem[]>([]);
  const navigate = useNavigate();

  useEffect(() => {
    setItems(getCart());
  }, []);

  const updateQty = (id:number, quantidade:number) => {
    updateCart(id, quantidade);
    setItems(getCart());
  };

  const total = items.reduce((acc,e) => acc + e.preco * e.quantidade, 0);

  return (
    <section className="container">
      <h2 className="secao-titulo">Carrinho</h2>
      {items.length === 0 ? (
        <p>Seu carrinho está vazio. <Link to="/">Voltar às compras</Link></p>
      ) : (
        <>
          <table className="tabela-carrinho">
            <thead><tr><th>Produto</th><th>Qtd.</th><th>Preço</th><th>Subtotal</th><th></th></tr></thead>
            <tbody>
              {items.map(item => (
                <tr key={item.produto_id}>
                  <td>{item.nome}</td>
                  <td><input type="number" min={1} value={item.quantidade} onChange={(e)=>updateQty(item.produto_id, Number(e.target.value))} /></td>
                  <td>R$ {item.preco.toFixed(2)}</td>
                  <td>R$ {(item.preco * item.quantidade).toFixed(2)}</td>
                  <td><button onClick={() => updateQty(item.produto_id, 0)}>Remover</button></td>
                </tr>
              ))}
            </tbody>
          </table>
          <div className="total-area">
            <strong>Total: R$ {total.toFixed(2)}</strong>
            <button className="btn-primary" onClick={() => navigate('/checkout')}>Finalizar Pedido</button>
            <button className="btn-outline" onClick={() => { clearCart(); setItems([]); }}>Limpar Carrinho</button>
          </div>
        </>
      )}
    </section>
  );
}
