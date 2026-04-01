import { FormEvent, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { getCart, clearCart } from '../store/cart';
import { placeOrder } from '../services/api';

export default function Checkout() {
  const navigate = useNavigate();
  const [nome, setNome] = useState('');
  const [telefone, setTelefone] = useState('');
  const [endereco, setEndereco] = useState('');
  const [forma, setForma] = useState('pix');
  const [erro, setErro] = useState('');
  const [sucesso, setSucesso] = useState('');

  const itens = getCart();

  async function onSubmit(e: FormEvent) {
    e.preventDefault();

    if (!nome || !endereco || itens.length === 0) {
      setErro('Complete os dados e tenha itens no carrinho.');
      return;
    }

    try {
      const data = { userId: null, nome_cliente: nome, telefone, endereco, forma_pagamento: forma, itens };
      const resp = await placeOrder(data);
      setSucesso('Pedido realizado com sucesso!');
      clearCart();
      navigate(`/confirmacao/${resp.pedidoId}`);
    } catch (err: any) {
      setErro(err?.response?.data?.error || 'Erro ao finalizar o pedido.');
    }
  }

  return (
    <section className="container">
      <h2 className="secao-titulo">Checkout</h2>
      {erro && <div className="alerta alerta-erro">{erro}</div>}
      {sucesso && <div className="alerta alerta-sucesso">{sucesso}</div>}
      <form className="form-checkout" onSubmit={onSubmit}>
        <div className="campo"><label>Nome</label><input required value={nome} onChange={(e)=>setNome(e.target.value)}/></div>
        <div className="campo"><label>Telefone</label><input value={telefone} onChange={(e)=>setTelefone(e.target.value)}/></div>
        <div className="campo"><label>Endereço</label><textarea required value={endereco} onChange={(e)=>setEndereco(e.target.value)}/></div>
        <div className="campo"><label>Forma de pagamento</label><select value={forma} onChange={(e)=>setForma(e.target.value)}><option value="pix">PIX</option><option value="cartao">Cartão</option><option value="dinheiro">Dinheiro</option></select></div>
        <button type="submit" className="btn-primary btn-full">Enviar pedido</button>
      </form>
    </section>
  );
}
