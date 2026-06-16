<?php
include("../config/protecao.php");
include("../config/conexao.php");

if (
    $_SESSION['usuario_tipo'] != 'admin' &&
    $_SESSION['usuario_tipo'] != 'tecnico'
) {
    echo "Acesso negado!";
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Histórico de Manutenções</title>

    <link rel="stylesheet" href="../assents/css/style.css">
    <script src="../assents/js/script.js"></script>
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
            <strong>Histórico de Manutenções</strong>
        </div>

        <div class="card">

            <h3>Manutenções Concluídas</h3>

            <table border="1" width="100%" cellpadding="10">

                <tr>
                    <th>Equipamento</th>
                    <th>Problema</th>
                    <th>Data Início</th>
                    <th>Data Final</th>
                    <th>Status</th>
                </tr>

                <?php

                $sql = "SELECT m.*, e.codigo as equipamento
                    FROM manutencoes m
                    JOIN equipamentos e
                    ON m.equipamento_id = e.id
                    WHERE m.status='concluida'
                    ORDER BY m.data_fim DESC";

                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {

                    echo "<tr>

                        <td>{$row['equipamento']}</td>

                        <td>{$row['descricao']}</td>

                        <td>{$row['data_inicio']}</td>

                        <td>{$row['data_fim']}</td>

                        <td>
                            <span class='status-disponivel'>
                                Concluída
                            </span>
                        </td>

                      </tr>";
                }

                ?>

            </table>

            <h3>Histórico de Reservas</h3>

            <table border="1" width="100%" cellpadding="10">

                <tr>
                    <th>Professor</th>
                    <th>Equipamento</th>
                    <th>Data Reserva</th>
                    <th>Hora Início</th>
                    <th>Hora Fim</th>
                    <th>Status</th>
                </tr>

                <?php

                $sql = "SELECT r.*, 
                   u.nome AS professor,
                   e.codigo AS equipamento
            FROM reservas r
            JOIN usuarios u
                ON r.usuario_id = u.id
            JOIN equipamentos e
                ON r.equipamento_id = e.id
            ORDER BY r.data_reserva DESC";

                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {

                    echo "<tr>

                <td>{$row['professor']}</td>

                <td>{$row['equipamento']}</td>

                <td>{$row['data_reserva']}</td>

                <td>{$row['hora_inicio']}</td>

                <td>{$row['hora_fim']}</td>

                <td>{$row['status']}</td>

              </tr>";
                }

                ?>

            </table>

        </div>

    </div>

</body>

</html>