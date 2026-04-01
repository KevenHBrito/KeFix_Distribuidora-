@echo off
echo ============================================
echo KeFix Node.js - Instalacao Automatica
echo ============================================
echo.

echo Verificando Node.js...
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERRO: Node.js nao esta instalado!
    echo Baixe em: https://nodejs.org/
    pause
    exit /b 1
)

echo Verificando npm...
npm --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERRO: npm nao esta instalado!
    pause
    exit /b 1
)

echo.
echo Instalando dependencias do backend...
cd backend
call npm install
if %errorlevel% neq 0 (
    echo ERRO: Falha ao instalar dependencias do backend!
    pause
    exit /b 1
)

echo.
echo Instalando dependencias do frontend...
cd ../frontend
call npm install
if %errorlevel% neq 0 (
    echo ERRO: Falha ao instalar dependencias do frontend!
    pause
    exit /b 1
)

cd ..
echo.
echo Instalacao concluida com sucesso!
echo.
echo Proximos passos:
echo 1. Certifique-se de que o MySQL esta rodando (XAMPP)
echo 2. Importe o database.sql para criar a base kefix_db
echo 3. Execute: npm run dev
echo.
pause