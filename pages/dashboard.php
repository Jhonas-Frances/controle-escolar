<?php

// Verifica se o usuário está logado
include("../config/protecao.php");

// Conexão com o banco de dados
include("../config/conexao.php");

// Verifica se o usuário possui permissão para acessar o dashboard
if (
    $_SESSION['usuario_tipo'] != 'admin' &&
    $_SESSION['usuario_tipo'] != 'professor' &&
    $_SESSION['usuario_tipo'] != 'tecnico'
) {
    echo "Acesso negado!";
    exit();
}

// Armazena o tipo do usuário logado
$tipo = $_SESSION['usuario_tipo'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- Arquivo de estilos do sistema -->
    <link rel="stylesheet" href="../assents/css/style.css?v=3">
</head>

<body>

<!-- Menu lateral -->
<div class="sidebar">
    <h2>Controle Escolar</h2>

    <!-- Link principal -->
    <a href="dashboard.php">Dashboard</a>

    <!-- Menu exclusivo para administrador -->
    <?php if ($_SESSION['usuario_tipo'] == 'admin'): ?>
    <a href="usuarios.php">Usuários</a>
    <a href="equipamentos.php">Equipamentos</a>
    <a href="reservas.php">Reservas</a>
    <a href="manutencoes.php">Manutenções</a>
    <a href="historico_manutencoes.php">Histórico</a>
<?php endif; ?>

    <!-- Menu exclusivo para professor -->
    <?php if ($_SESSION['usuario_tipo'] == 'professor'): ?>
    <a href="reservas.php">Reservas</a>
    <a href="manutencoes.php">Manutenções</a>
<?php endif; ?>

    <!-- Menu exclusivo para técnico -->
    <?php if ($_SESSION['usuario_tipo'] == 'tecnico'): ?>
    <a href="equipamentos.php">Equipamentos</a>
    <a href="reservas.php">Reservas</a>
    <a href="manutencoes.php">Manutenções</a>
    <a href="historico_manutencoes.php">Histórico</a>
<?php endif; ?>

    <!-- Encerra a sessão do usuário -->
    <a href="../logout.php">Sair</a>
</div>

<!-- Área principal do sistema -->
<div class="content">

    <!-- Barra superior com o nome do usuário logado -->
    <div class="topbar">
        <strong>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?></strong>
    </div>

</body>
</html>