import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { fetchDestaques, fetchCategorias, Categoria, Produto } from '../services/api';
import { addToCart } from '../store/cart';
import { Smartphone, Battery, Zap, Volume2, Mic, Layers, Camera } from 'lucide-react';

const iconMap: Record<string, any> = {
  'monitor': Smartphone,
  'battery-charging': Battery,
  'zap': Zap,
  'volume-2': Volume2,
  'mic': Mic,
  'smartphone': Smartphone,
  'layers': Layers,
  'camera': Camera,
};

export default function Home() {
  const [categorias, setCategorias] = useState<Categoria[]>([]);
  const [destaques, setDestaques] = useState<Produto[]>([]);

  useEffect(() => {
    fetchCategorias().then(setCategorias).catch(console.error);
    fetchDestaques().then(setDestaques).catch(console.error);
  }, []);

  return (
    <div>
      <section className="hero">
        <div className="hero-content container">
          <div className="hero-texto">
            <span className="hero-badge">🔧 Distribuidora Oficial</span>
            <h1>Peças originais para <span className="destaque-texto">qualquer celular</span></h1>
            <p>Telas, baterias, conectores e muito mais.</p>
            <div className="hero-botoes">
              <Link to="#produtos" className="btn-primary">Ver produtos</Link>
              <Link to="/login" className="btn-outline">Criar conta grátis</Link>
            </div>
          </div>
          <div className="hero-img"><div className="hero-circle"><Smartphone size={80} color="#007BFF" /></div></div>
        </div>
      </section>

      <section className="secao-categorias">
        <div className="container">
          <h2 className="secao-titulo">Categorias</h2>
          <div className="grid-categorias">
            {categorias.map((cat) => {
              const IconComponent = iconMap[cat.icone] || Smartphone;
              return (
                <Link key={cat.id} to={`/categoria/${cat.slug}`} className="card-categoria">
                  <IconComponent size={40} />
                  <span>{cat.nome}</span>
                </Link>
              );
            })}
          </div>
        </div>
      </section>

      <section className="secao-produtos" id="produtos">
        <div className="container">
          <div className="secao-header">
            <h2 className="secao-titulo">Produtos em Destaque</h2>
            <Link to="/categoria/todos" className="ver-todos">Ver todos</Link>
          </div>
          <div className="grid-produtos">
            {destaques.map((p) => (
              <article key={p.id} className="card-produto">
                <Link to={`/produto/${p.id}`} className="card-img">
                  <img src={`/images/produtos/${p.imagem ?? 'sem-imagem.png'}`} alt={p.nome} onError={(e) => (e.currentTarget.src = '/images/sem-imagem.png')} />
                </Link>
                <div className="card-info">
                  <span className="card-categoria">{p.categoria_nome}</span>
                  <h3><Link to={`/produto/${p.id}`}>{p.nome}</Link></h3>
                  <div className="card-rodape">
                    <strong className="card-preco">R$ {p.preco.toFixed(2)}</strong>
                    <button className="btn-add-carrinho" onClick={() => addToCart({ produto_id:p.id, nome:p.nome, preco:p.preco, imagem:p.imagem, quantidade:1 })}>+</button>
                  </div>
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
