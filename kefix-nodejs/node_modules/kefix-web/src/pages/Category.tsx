import { useEffect, useState } from 'react';
import { useParams, useSearchParams } from 'react-router-dom';
import { fetchProdutos, Produto } from '../services/api';
import { addToCart } from '../store/cart';

export default function Category() {
  const params = useParams();
  const slug = params.slug || 'todos';
  const [searchParams] = useSearchParams();
  const search = searchParams.get('search') || '';
  const [produtos, setProdutos] = useState<Produto[]>([]);

  useEffect(() => {
    fetchProdutos({ category: slug === 'todos' ? undefined : slug, search: search || undefined })
      .then(setProdutos).catch(console.error);
  }, [slug, search]);

  return (
    <section className="container">
      <h2 className="secao-titulo">{slug === 'todos' ? 'Todos os produtos' : `Categoria: ${slug}`}</h2>
      <div className="grid-produtos">
        {produtos.map((p) => (
          <article key={p.id} className="card-produto">
            <a href={`/produto/${p.id}`} className="card-img">
              <img src={`/images/produtos/${p.imagem ?? 'sem-imagem.png'}`} alt={p.nome} />
            </a>
            <div className="card-info">
              <span className="card-categoria">{p.categoria_nome}</span>
              <h3><a href={`/produto/${p.id}`}>{p.nome}</a></h3>
              <div className="card-rodape">
                <strong className="card-preco">R$ {p.preco.toFixed(2)}</strong>
                <button onClick={() => addToCart({ produto_id:p.id, nome:p.nome, preco:p.preco, imagem:p.imagem, quantidade:1 })}>+</button>
              </div>
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}
