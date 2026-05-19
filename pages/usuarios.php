<?php
include("../config/protecao.php");
include("../config/conexao.php");

if ($_SESSION['usuario_tipo'] != 'admin') {
    echo "Acesso negado!";
    exit();
}


// CADASTRAR USUÁRIO
if (isset($_POST['cadastrar'])) {

    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $tipo = $_POST['tipo'];

    $iniciais = strtoupper(substr($nome, 0, 3));
    $senha = $iniciais . $matricula;

    $check = $conn->query("SELECT id FROM usuarios WHERE matricula = '$matricula'");

    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO usuarios (nome, matricula, senha, tipo)
                      VALUES ('$nome', '$matricula', '$senha', '$tipo')");
    }
}

// EXCLUIR USUÁRIO
if (isset($_GET['excluir'])) {

    $id = $_GET['excluir'];

    try {

        $conn->query("DELETE FROM usuarios WHERE id = $id");

    } catch (mysqli_sql_exception $e) {

        echo "<script>
                alert('Não é possível excluir este usuário porque ele possui reservas cadastradas.');
              </script>";
    }
}

// LISTAR USUÁRIOS
$result = $conn->query("SELECT * FROM usuarios");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
    <link rel="stylesheet" href="../assents/css/style.css">
    <script src="../assents/js/script.js"></script>
</head>

<body>

<div class="sidebar">
    <h2>Controle Escolar</h2>

    <a href="dashboard.php">Dashboard</a>

    <a href="usuarios.php">Usuários</a>
    <a href="equipamentos.php">Equipamentos</a>
    <a href="reservas.php">Reservas</a>
    <a href="manutencoes.php">Manutenções</a>
    <a href="historico_manutencoes.php">Histórico</a>

    <a href="../logout.php">Sair</a>
</div>

<div class="content">

    <div class="topbar">
        <strong>Gerenciar Usuários</strong>
    </div>

    <div class="card">

        <h3>Cadastrar Usuário</h3>

        <form method="POST">
            <input type="text" name="nome" placeholder="Nome" required><br><br>
            <input type="text" name="matricula" placeholder="Matrícula" required><br><br>

            <select name="tipo" required>
                <option value="">Tipo</option>
                <option value="admin">Administrador</option>
                <option value="professor">Professor</option>
                <option value="tecnico">Técnico</option>
            </select><br><br>

            <button type="submit" name="cadastrar">Cadastrar</button>
        </form>

    </div>

    <div class="card">

        <h3>Lista de Usuários</h3>

        <table border="1" width="100%" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Tipo</th>
                <th>Ação</th>
            </tr>

            <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['nome']; ?></td>
                <td><?php echo $user['matricula']; ?></td>
                <td><?php echo $user['tipo']; ?></td>
                <td>
                    <a href="?excluir=<?php echo $user['id']; ?>"
                       onclick="return confirmarExclusao()">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>

        </table>

    </div>

</div>

</body>
</html>