import { FormEvent, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { login, register } from '../services/api';

export default function Auth() {
  const [isRegister, setIsRegister] = useState(false);
  const [nome, setNome] = useState('');
  const [email, setEmail] = useState('');
  const [senha, setSenha] = useState('');
  const [telefone, setTelefone] = useState('');
  const [erro, setErro] = useState('');
  const navigate = useNavigate();

  async function onSubmit(e: FormEvent) {
    e.preventDefault();
    try {
      if (isRegister) {
        await register(nome, email, senha, telefone);
      } else {
        await login(email, senha);
      }
      navigate('/');
    } catch (error: any) {
      setErro(error?.response?.data?.error || 'Falha ao autenticar');
    }
  }

  return (
    <section className="container auth-main">
      <div className="auth-card">
        <h2>{isRegister ? 'Criar conta' : 'Entrar'}</h2>
        {erro && <div className="alerta alerta-erro">{erro}</div>}
        <form onSubmit={onSubmit}>
          {isRegister && <div className="campo"><label>Nome</label><input required value={nome} onChange={(e)=>setNome(e.target.value)} /></div>}
          <div className="campo"><label>E-mail</label><input required type="email" value={email} onChange={(e)=>setEmail(e.target.value)} /></div>
          <div className="campo"><label>Senha</label><input required type="password" value={senha} onChange={(e)=>setSenha(e.target.value)} /></div>
          {isRegister && <div className="campo"><label>Telefone</label><input value={telefone} onChange={(e)=>setTelefone(e.target.value)} /></div>}
          <button className="btn-primary btn-full" type="submit">{isRegister ? 'Cadastrar' : 'Entrar'}</button>
        </form>
        <p className="auth-link">{isRegister ? 'Já tem conta?' : 'Não tem conta?'} <button type="button" className="btn-link" onClick={() => setIsRegister(!isRegister)}>{isRegister ? 'Entrar' : 'Cadastre-se'}</button></p>
      </div>
    </section>
  );
}
