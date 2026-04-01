import { Route, Routes } from 'react-router-dom';
import Home from './pages/Home';
import Product from './pages/Product';
import Category from './pages/Category';
import Cart from './pages/Cart';
import Checkout from './pages/Checkout';
import Confirmation from './pages/Confirmation';
import Auth from './pages/Auth';
import Account from './pages/Account';
import Header from './components/Header';

function App() {
  return (
    <div className="app">
      <Header />
      <main>
        <Routes>
          <Route path="/" element={<Home/>} />
          <Route path="/categoria" element={<Category/>} />
          <Route path="/categoria/:slug" element={<Category/>} />
          <Route path="/produto/:id" element={<Product/>} />
          <Route path="/carrinho" element={<Cart/>} />
          <Route path="/checkout" element={<Checkout/>} />
          <Route path="/confirmacao/:id" element={<Confirmation/>} />
          <Route path="/login" element={<Auth/>} />
          <Route path="/minha-conta" element={<Account/>} />
        </Routes>
      </main>
    </div>
  );
}

export default App;
