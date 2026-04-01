import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { fetchProduto, Produto } from '../services/api';
import { addToCart } from '../store/cart';

export default function Product() {
  const { id } = useParams();
  const [produto, setProduto] = useState<Produto | null>(null);
  const [quantidade, setQuantidade] = useState(1);

  useEffect(() => {
    if (!id) return;
    fetchProduto(Number(id)).then(setProduto).catch(console.error);
  }, [id]);

  if (!produto) return <div className="container">Carregando...</div>;

  return (
    <section className="container secao-produto">
      <div className="produto-grid">
        <img src={`/images/produtos/${produto.imagem ?? 'sem-imagem.png'}`} alt={produto.nome} />
        <div>
          <h1>{produto.nome}</h1>
          <p>{produto.descricao}</p>
          <strong>R$ {produto.preco.toFixed(2)}</strong>
          <div className="produto-actions">
            <input type="number" min={1} value={quantidade} onChange={(e) => setQuantidade(Number(e.target.value))} />
            <button className="btn-primary" onClick={() => addToCart({ produto_id:produto.id, nome:produto.nome, preco:produto.preco, imagem:produto.imagem, quantidade })}>Adicionar ao carrinho</button>
          </div>
          <p>{produto.estoque === 0 ? 'Esgotado' : `Estoque: ${produto.estoque}`}</p>
        </div>
      </div>
    </section>
  );
}
