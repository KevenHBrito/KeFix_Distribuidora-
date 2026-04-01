import { Link, useNavigate } from 'react-router-dom';
import { ShoppingCart, Search, User, Menu } from 'lucide-react';
import { useEffect, useState } from 'react';
import { countCartItems, getCart } from '../store/cart';

export default function Header() {
  const [count, setCount] = useState(0);
  const navigate = useNavigate();

  useEffect(() => {
    setCount(countCartItems());
  }, []);

  return (
    <header className="header">
      <div className="container header-inner">
        <Link to="/" className="logo">
          <span className="logo-ke">Ke</span><span className="logo-fix">Fix</span>
        </Link>

        <div className="busca">
          <input type="search" placeholder="Buscar produtos..." onKeyDown={(e) => {
            if (e.key === 'Enter') {
              const value = (e.target as HTMLInputElement).value.trim();
              if (value) navigate(`/categoria/todos?search=${encodeURIComponent(value)}`);
            }
          }} />
          <button type="button" onClick={(e) => {
            const input = (e.currentTarget.previousElementSibling as HTMLInputElement);
            const value = input?.value.trim();
            if (value) navigate(`/categoria/todos?search=${encodeURIComponent(value)}`);
          }}><Search size={16} /></button>
        </div>

        <div className="header-acoes">
          <Link to="/minha-conta" className="btn-header"><User size={20} /><span>Conta</span></Link>
          <Link to="/carrinho" className="btn-header btn-carrinho">
            <ShoppingCart size={20}/>
            {count > 0 && <span className="badge-carrinho">{count}</span>}
            <span>Carrinho</span>
          </Link>
          <button className="btn-menu-mobile"><Menu/></button>
        </div>
      </div>
    </header>
  );
}
