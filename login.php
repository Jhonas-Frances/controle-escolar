<?php
// Inicia a sessão para armazenar os dados do usuário logado
session_start();

// Importa a conexão com o banco de dados
include("config/conexao.php");

// Variável responsável por armazenar mensagens de erro
$erro = "";

// Verifica se o botão "Entrar" foi clicado
if (isset($_POST['entrar'])) {

    // Recebe e remove espaços extras dos campos digitados
    $matricula = trim($_POST['matricula']);
    $senha = trim($_POST['senha']);

    // Verifica se os campos foram preenchidos
    if (!empty($matricula) && !empty($senha)) {

        // Consulta o usuário pela matrícula utilizando Prepared Statement
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE matricula = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica se encontrou algum usuário com a matrícula informada
        if ($result->num_rows > 0) {

            // Obtém os dados do usuário encontrado
            $user = $result->fetch_assoc();

            // Valida a senha informada pelo usuário
            // Neste caso está sendo utilizada comparação simples
            if ($senha == $user['senha']) {

                // Armazena os dados do usuário na sessão
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                $_SESSION['usuario_tipo'] = $user['tipo'];

                // Redireciona para o dashboard após login bem-sucedido
                header("Location: pages/dashboard.php");
                exit();

            } else {
                // Mensagem caso a senha esteja incorreta
                $erro = "Senha incorreta!";
            }

        } else {
            // Mensagem caso a matrícula não seja encontrada
            $erro = "Usuário não encontrado!";
        }

    } else {
        // Mensagem caso algum campo esteja vazio
        $erro = "Preencha todos os campos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login - Sistema</title>

<!-- Importação da fonte Poppins do Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

/* Reset básico da página */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

/* Estilização geral da página */
body {
    height: 100vh;
    background: linear-gradient(135deg, #1cd31f, #f3f5f2);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Container principal do login */
.container {
    display: flex;
    width: 900px;
    height: 520px;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

/* Área esquerda com informações do sistema */
.left {
    width: 50%;
    background: linear-gradient(135deg, #2a5298, #1e3c72);
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 30px;
}

.left h1 {
    font-size: 30px;
    margin-bottom: 10px;
}

.left p {
    font-size: 15px;
    opacity: 0.9;
}

/* Área direita contendo o formulário de login */
.right {
    width: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 50px;
}

.right h2 {
    margin-bottom: 20px;
    color: #333;
}

/* Grupo dos campos de entrada */
.input-group {
    position: relative;
    margin-bottom: 15px;
}

.input-group input {
    width: 100%;
    padding: 12px;
    padding-right: 40px;
    border: 1px solid #ccc;
    border-radius: 10px;
    outline: none;
    transition: 0.3s;
}

.input-group input:focus {
    border-color: #2a5298;
}

/* Ícone utilizado para mostrar/ocultar senha */
.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}

/* Estilo do botão de login */
button {
    width: 100%;
    padding: 12px;
    border: none;
    background: #2a5298;
    color: white;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}

button:hover {
    background: #18dd5a;
}

/* Caixa de exibição de mensagens de erro */
.erro {
    background: #ffdddd;
    color: #a00;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
}

/* Ajustes para telas menores */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        width: 90%;
        height: auto;
    }

    .left, .right {
        width: 100%;
    }
}

</style>
</head>

<body>

<div class="container">

    <!-- Painel de apresentação do sistema -->
    <div class="left">
        <h1>Controle de Equipamentos</h1>
        <p>Gerencie reservas, usuários e manutenções</p>
    </div>

    <!-- Área do formulário de login -->
    <div class="right">
        <h2>Login</h2>

        <!-- Exibe mensagens de erro quando existirem -->
        <?php if (!empty($erro)) echo "<div class='erro'>$erro</div>"; ?>

        <form method="POST">

            <!-- Campo matrícula -->
            <div class="input-group">
                <input type="text" name="matricula" placeholder="Matrícula" required>
            </div>

            <!-- Campo senha -->
            <div class="input-group">
                <input type="password" name="senha" id="senha" placeholder="Senha" required>
                <span class="toggle-password" onclick="toggleSenha()">👁️</span>
            </div>

            <!-- Botão de envio -->
            <button type="submit" name="entrar">Entrar</button>

        </form>
    </div>

</div>

<script>
// Função responsável por mostrar ou ocultar a senha digitada
function toggleSenha() {
    const input = document.getElementById("senha");
    input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>