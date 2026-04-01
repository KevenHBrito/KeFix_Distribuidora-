import { useParams, Link } from 'react-router-dom';

export default function Confirmation() {
  const { id } = useParams();
  return (
    <section className="container">
      <h2 className="secao-titulo">Pedido Confirmado</h2>
      <div className="card-confirmacao">
        <p>Seu pedido <b>#{id}</b> foi registrado com sucesso.</p>
        <p>Obrigado pela compra! Em breve entraremos em contato para logística.</p>
        <Link to="/">Voltar à loja</Link>
      </div>
    </section>
  );
}
