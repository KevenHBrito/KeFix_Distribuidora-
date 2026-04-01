import { useEffect, useState } from 'react';
import { fetchOrders } from '../services/api';

export default function Account() {
  const [orders, setOrders] = useState<any[]>([]);
  const userId = 1;

  useEffect(() => {
    fetchOrders(userId).then(setOrders).catch(console.error);
  }, [userId]);

  return (
    <section className="container">
      <h2 className="secao-titulo">Minha Conta</h2>
      <div className="card">
        <p>Usuário: usuário logado</p>
        <p>Email: usuário@exemplo.com</p>
      </div>
      <h3>Pedidos</h3>
      <ul className="pedido-list">
        {orders.map((order) => (
          <li key={order.id}>
            <strong>#{order.id}</strong> - {order.status} - R$ {parseFloat(order.total).toFixed(2)}
          </li>
        ))}
      </ul>
    </section>
  );
}
