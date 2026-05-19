<?php
session_start();
include("config/conexao.php");

// 🔒 PROTEÇÃO: só usuário logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// 🔒 PROTEÇÃO: só admin
if ($_SESSION['usuario_tipo'] != 'admin') {
    echo "Acesso negado!";
    exit();
}

$mensagem = "";

if (isset($_POST['cadastrar'])) {

    // TRATAR DADOS
    $nome = trim($_POST['nome']);
    $matricula = trim($_POST['matricula']);
    $tipo = $_POST['tipo'];

    // VALIDAÇÃO
    if (empty($nome) || empty($matricula) || empty($tipo)) {
        $mensagem = "<p style='color:red;'>Preencha todos os campos!</p>";
    } else {

        // GERAR SENHA
        $iniciais = strtoupper(substr($nome, 0, 3));
        $senha = $iniciais . $matricula;

        // VERIFICAR DUPLICIDADE
        $check = $conn->query("SELECT id FROM usuarios WHERE matricula = '$matricula'");

        if ($check && $check->num_rows > 0) {

            $mensagem = "<p style='color:red;'>Matrícula já cadastrada!</p>";

        } else {

            // INSERIR
            $sql = "INSERT INTO usuarios (nome, matricula, senha, tipo)
                    VALUES ('$nome', '$matricula', '$senha', '$tipo')";

            if ($conn->query($sql)) {

                $mensagem = "<p style='color:green;'>Usuário cadastrado com sucesso!</p>";
                $mensagem .= "<p>Senha padrão: <b>$senha</b></p>";

            } else {

                $mensagem = "<p style='color:red;'>Erro ao cadastrar: " . $conn->error . "</p>";

            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>

    <!-- CSS DO SISTEMA -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="content">

    <div class="topbar">
        <strong>Cadastrar Usuário</strong>
    </div>

    <div class="card">

        <p>Logado como: <b><?php echo $_SESSION['usuario_nome']; ?></b></p>

        <?php echo $mensagem; ?>

        <form method="POST">

            <input type="text" name="nome" placeholder="Nome" required>

            <input type="text" name="matricula" placeholder="Matrícula" required>

            <select name="tipo" required>
                <option value="">Selecione o tipo</option>
                <option value="admin">Administrador</option>
                <option value="professor">Professor</option>
                <option value="tecnico">Técnico</option>
            </select>

            <button type="submit" name="cadastrar">Cadastrar</button>

        </form>

    </div>

    <br>

    <a href="pages/dashboard.php">← Voltar ao Dashboard</a>

</div>

</body>
</html>