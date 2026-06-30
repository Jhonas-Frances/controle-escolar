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

// Busca clima de Cataguases na API
$url = "https://api.open-meteo.com/v1/forecast?latitude=-21.39&longitude=-42.70&current=temperature_2m,weather_code";

// Recebe resposta da API
$resposta = file_get_contents($url);

// Converte JSON para array PHP
$dados = json_decode($resposta, true);

// Obtém temperatura atual
$temperatura = $dados['current']['temperature_2m'];

// Obtém código da condição climática
$codigo = $dados['current']['weather_code'];

// Valor padrão do clima
$clima = "Não informado";

// Traduz código da API para texto
if ($codigo == 0) {
    $clima = "Ensolarado";
} elseif ($codigo <= 3) {
    $clima = "Parcialmente nublado";
} elseif ($codigo <= 48) {
    $clima = "Nublado";
} else {
    $clima = "Chuva";
}

// Total de equipamentos
$totalEquipamentos = $conn->query("
    SELECT COUNT(*) AS total
    FROM equipamentos
")->fetch_assoc()['total'];

// Total de reservas
$totalReservas = $conn->query("
    SELECT COUNT(*) AS total
    FROM reservas
")->fetch_assoc()['total'];

// Manutenções abertas
$totalManutencoes = $conn->query("
    SELECT COUNT(*) AS total
    FROM manutencoes
    WHERE status='aberta'
")->fetch_assoc()['total'];

// Manutenções concluídas
$manutencoesConcluidas = $conn->query("
    SELECT COUNT(*) AS total
    FROM manutencoes
    WHERE status='concluida'
")->fetch_assoc()['total'];

// Reservas finalizadas
$reservasFinalizadas = $conn->query("
    SELECT COUNT(*) AS total
    FROM reservas
    WHERE status='finalizada'
")->fetch_assoc()['total'];

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- Arquivo de estilos do sistema -->
    <link rel="stylesheet" href="../assents/css/style.css?v=4">
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

        <div class="card">

            <h3>Clima Atual - Cataguases/MG</h3>

            <p><strong>Temperatura:</strong> <?php echo $temperatura; ?>°C</p>

            <p><strong>Condição:</strong> <?php echo $clima; ?></p>

        </div>
        <div class="dashboard-cards">

            <div class="card-resumo">
                <h3>Equipamentos</h3>
                <h1><?php echo $totalEquipamentos; ?></h1>
            </div>

            <div class="card-resumo">
                <h3>Reservas</h3>
                <h1><?php echo $totalReservas; ?></h1>
            </div>

            <div class="card-resumo">
                <h3>Em Manutenção</h3>
                <h1><?php echo $totalManutencoes; ?></h1>
            </div>

            <div class="card-resumo">
                <h3>Manutenções Concluídas</h3>
                <h1><?php echo $manutencoesConcluidas; ?></h1>
            </div>

            <div class="card-resumo">
                <h3>Reservas Finalizadas</h3>
                <h1><?php echo $reservasFinalizadas; ?></h1>
            </div>

        </div>

    </div>
</body>

</html>