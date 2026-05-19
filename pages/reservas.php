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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reservas</title>

    <link rel="stylesheet" href="../assents/css/style.css?v=2">
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
        <strong>Reservar Equipamento</strong>
    </div>

    <!-- FILTRO -->
    <div class="card">
        <h3>Filtrar por Local</h3>

        <form method="GET">
            <select name="local_id" onchange="this.form.submit()">
                <option value="">Todos os locais</option>

                <?php
                $locais = $conn->query("SELECT * FROM locais");
                while ($l = $locais->fetch_assoc()) {
                    $selected = (isset($_GET['local_id']) && $_GET['local_id'] == $l['id']) ? "selected" : "";
                    echo "<option value='{$l['id']}' $selected>{$l['nome']}</option>";
                }
                ?>
            </select>
        </form>
    </div>

    <!-- LISTA DE EQUIPAMENTOS -->
    <div class="card">

        <h3>Equipamentos</h3>

        <table>
            <tr>
                <th>Código</th>
                <th>Tipo</th>
                <th>Local</th>
                <th>Status</th>
            </tr>

            <?php

            $filtro = "";
            if (!empty($_GET['local_id'])) {
                $local_id = $_GET['local_id'];
                $filtro = "WHERE e.local_id = $local_id";
            }

            $sql = "
            SELECT 
                e.id,
                e.codigo,
                t.nome AS tipo,
                l.nome AS local,

                (SELECT COUNT(*) FROM reservas r 
                 WHERE r.equipamento_id = e.id AND r.status='ativa') AS em_uso,

                (SELECT COUNT(*) FROM manutencoes m 
                 WHERE m.equipamento_id = e.id AND m.status='aberta') AS em_manutencao

            FROM equipamentos e
            LEFT JOIN tipos_equipamento t ON e.tipo_id = t.id
            LEFT JOIN locais l ON e.local_id = l.id
            $filtro
            ";

            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {

                echo "<tr>";
                echo "<td>{$row['codigo']}</td>";
                echo "<td>{$row['tipo']}</td>";
                echo "<td>{$row['local']}</td>";
                echo "<td>";

                if ($row['em_manutencao'] > 0) {
                    echo "<span class='status-manutencao'>Em manutenção</span>";
                } elseif ($row['em_uso'] > 0) {
                    echo "<span class='status-ocupado'>Ocupado</span>";
                } else {
                    echo "<span class='status-disponivel'>Disponível</span>";
                }

                echo "</td>";
                echo "</tr>";
            }

            ?>

        </table>

    </div>

    <!-- FORM RESERVA -->
    <div class="card">

        <h3>Nova Reserva</h3>

        <form method="POST">

            <select name="equipamento_id" required>
                <option value="">Selecione o equipamento</option>

                <?php

                $equipamentos = $conn->query("
                    SELECT e.id, e.codigo, t.nome AS tipo,

                    (SELECT COUNT(*) FROM reservas r 
                     WHERE r.equipamento_id = e.id AND r.status='ativa') AS em_uso,

                    (SELECT COUNT(*) FROM manutencoes m 
                     WHERE m.equipamento_id = e.id AND m.status='aberta') AS em_manutencao

                    FROM equipamentos e
                    LEFT JOIN tipos_equipamento t ON e.tipo_id = t.id
                ");

                while ($eq = $equipamentos->fetch_assoc()) {

                    // BLOQUEIA indisponíveis
                    if ($eq['em_uso'] > 0 || $eq['em_manutencao'] > 0) {
                        continue;
                    }

                    echo "<option value='{$eq['id']}'>
                            {$eq['codigo']} - {$eq['tipo']}
                          </option>";
                }
                ?>
            </select>

            <input type="date" name="data_reserva" required>

            <label>Hora início:</label>
            <input type="time" name="hora_inicio" required>

            <label>Hora fim:</label>
            <input type="time" name="hora_fim" required>

            <button type="submit" name="reservar">Reservar</button>

        </form>

        <?php
        if (isset($_POST['reservar'])) {

            $equipamento_id = $_POST['equipamento_id'];
            $data = $_POST['data_reserva'];
            $hora_inicio = $_POST['hora_inicio'];
            $hora_fim = $_POST['hora_fim'];
            $usuario_id = $_SESSION['usuario_id'];

            $verifica = $conn->query("
                SELECT * FROM reservas 
                WHERE equipamento_id = $equipamento_id
                AND data_reserva = '$data'
                AND status = 'ativa'
                AND (
                    ('$hora_inicio' BETWEEN hora_inicio AND hora_fim)
                    OR ('$hora_fim' BETWEEN hora_inicio AND hora_fim)
                )
            ");

            if ($verifica->num_rows > 0) {
                echo "<p style='color:red;'>Equipamento já reservado!</p>";
            } else {

                $conn->query("
                    INSERT INTO reservas 
                    (usuario_id, equipamento_id, data_reserva, hora_inicio, hora_fim, status)
                    VALUES ($usuario_id, $equipamento_id, '$data', '$hora_inicio', '$hora_fim', 'ativa')
                ");

                echo "<p style='color:green;'>Reserva realizada!</p>";
            }
        }
        ?>

    </div>

</div>

</body>
</html>