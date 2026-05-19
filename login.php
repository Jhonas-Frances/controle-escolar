<?php
session_start();
include("config/conexao.php");

$erro = "";

if (isset($_POST['entrar'])) {

    $matricula = trim($_POST['matricula']);
    $senha = trim($_POST['senha']);

    if (!empty($matricula) && !empty($senha)) {

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE matricula = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $user = $result->fetch_assoc();

            // 🔐 Se estiver usando senha simples:
            if ($senha == $user['senha']) {

                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                $_SESSION['usuario_tipo'] = $user['tipo'];

                header("Location: pages/dashboard.php");
                exit();

            } else {
                $erro = "Senha incorreta!";
            }

        } else {
            $erro = "Usuário não encontrado!";
        }

    } else {
        $erro = "Preencha todos os campos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login - Sistema</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    height: 100vh;
    background: linear-gradient(135deg, #1cd31f, #f3f5f2);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Container */
.container {
    display: flex;
    width: 900px;
    height: 520px;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

/* Lado esquerdo */
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

/* Lado direito */
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

/* Inputs */
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

/* Olho senha */
.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}

/* Botão */
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

/* Erro */
.erro {
    background: #ffdddd;
    color: #a00;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
}

/* Responsivo */
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

    <div class="left">
        <h1>Controle de Equipamentos</h1>
        <p>Gerencie reservas, usuários e manutenções</p>
    </div>

    <div class="right">
        <h2>Login</h2>

        <?php if (!empty($erro)) echo "<div class='erro'>$erro</div>"; ?>

        <form method="POST">

            <div class="input-group">
                <input type="text" name="matricula" placeholder="Matrícula" required>
            </div>

            <div class="input-group">
                <input type="password" name="senha" id="senha" placeholder="Senha" required>
                <span class="toggle-password" onclick="toggleSenha()">👁️</span>
            </div>

            <button type="submit" name="entrar">Entrar</button>

        </form>
    </div>

</div>

<script>
function toggleSenha() {
    const input = document.getElementById("senha");
    input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>