<?php
include("../config/protecao.php");
include("../config/conexao.php");

if (
    $_SESSION['usuario_tipo'] != 'admin' &&
    $_SESSION['usuario_tipo'] != 'professor' &&
    $_SESSION['usuario_tipo'] != 'tecnico'
) {
    echo "Acesso negado!";
    exit();
}



$tipo = $_SESSION['usuario_tipo'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <link rel="stylesheet" href="../assents/css/style.css?v=3">
</head>

<body>

<div class="sidebar">
    <h2>Controle Escolar</h2>

    <a href="dashboard.php">Dashboard</a>

    <?php if ($_SESSION['usuario_tipo'] == 'admin'): ?>
    <a href="usuarios.php">Usuários</a>
    <a href="equipamentos.php">Equipamentos</a>
    <a href="reservas.php">Reservas</a>
    <a href="manutencoes.php">Manutenções</a>
    <a href="historico_manutencoes.php">Histórico</a>
<?php endif; ?>

    <?php if ($_SESSION['usuario_tipo'] == 'professor'): ?>
    <a href="reservas.php">Reservas</a>
    <a href="manutencoes.php">Manutenções</a>
<?php endif; ?>

    <?php if ($_SESSION['usuario_tipo'] == 'tecnico'): ?>
    <a href="equipamentos.php">Equipamentos</a>
    <a href="reservas.php">Reservas</a>
    <a href="manutencoes.php">Manutenções</a>
    <a href="historico_manutencoes.php">Histórico</a>
<?php endif; ?>

    <a href="../logout.php">Sair</a>
</div>

<div class="content">

    <div class="topbar">
        <strong>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?></strong>
    </div>


</body>
</html>