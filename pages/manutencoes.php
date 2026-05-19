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
    <title>Manutenções</title>

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
        <strong>Manutenção de Equipamentos</strong>
    </div>

    <?php if ($_SESSION['usuario_tipo'] != 'professor'): ?>

    <!-- FILTRO -->
    <div class="card">

        <h3>Filtrar por Local</h3>

        <form method="GET">

            <select name="local_id" onchange="this.form.submit()">

                <option value="">Todos os locais</option>

                <?php

                $locais = $conn->query("SELECT * FROM locais");

                while ($l = $locais->fetch_assoc()) {

                    $selected = (
                        isset($_GET['local_id']) &&
                        $_GET['local_id'] == $l['id']
                    ) ? "selected" : "";

                    echo "<option value='{$l['id']}' $selected>
                            {$l['nome']}
                          </option>";
                }

                ?>

            </select>

        </form>

    </div>

    <!-- ABRIR MANUTENÇÃO -->
    <div class="card">

        <h3>Abrir Manutenção</h3>

        <form method="POST">

            <select name="equipamento_id" required>

                <option value="">Selecione um equipamento</option>

                <?php

                $filtro = "";

                if (
                    isset($_GET['local_id']) &&
                    !empty($_GET['local_id'])
                ) {

                    $local_id = $_GET['local_id'];

                    $filtro = "AND e.local_id = $local_id";
                }

                $equipamentos = $conn->query("

                    SELECT e.*

                    FROM equipamentos e

                    WHERE e.id NOT IN (

                        SELECT equipamento_id
                        FROM manutencoes
                        WHERE status='aberta'

                    )

                    $filtro

                ");

                while ($eq = $equipamentos->fetch_assoc()) {

                    echo "<option value='{$eq['id']}'>
                            {$eq['codigo']}
                          </option>";
                }

                ?>

            </select><br><br>

            <input type="text"
                   name="descricao"
                   placeholder="Descrição do problema"
                   required><br><br>

            <button type="submit" name="abrir">
                Abrir Manutenção
            </button>

        </form>

        <?php

        if (isset($_POST['abrir'])) {

            $equipamento_id = $_POST['equipamento_id'];
            $descricao = $_POST['descricao'];
            $data = date("Y-m-d");

            $sql = "INSERT INTO manutencoes
                    (equipamento_id, descricao, data_inicio, status)
                    VALUES
                    ($equipamento_id, '$descricao', '$data', 'aberta')";

            if ($conn->query($sql)) {

                echo "<p style='color:green;'>
                        Manutenção aberta com sucesso!
                      </p>";

            } else {

                echo "<p style='color:red;'>
                        Erro ao abrir manutenção!
                      </p>";
            }
        }

        ?>

    </div>

    <?php endif; ?>

    <!-- MANUTENÇÕES ABERTAS -->
    <div class="card">

        <h3>Manutenções Abertas</h3>

        <table border="1" width="100%" cellpadding="10">

            <tr>
                <th>Equipamento</th>
                <th>Descrição</th>
                <th>Data Início</th>
                <th>Status</th>
                <th>Ação</th>
            </tr>

            <?php

            $sql = "SELECT m.*, e.codigo as equipamento
                    FROM manutencoes m
                    JOIN equipamentos e
                    ON m.equipamento_id = e.id
                    WHERE m.status='aberta'";

            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {

                echo "<tr>

                        <td>{$row['equipamento']}</td>

                        <td>{$row['descricao']}</td>

                        <td>{$row['data_inicio']}</td>

                        <td>
                            <span class='status-manutencao'>
                                Em manutenção
                            </span>
                        </td>

                        <td>";

                if ($_SESSION['usuario_tipo'] != 'professor') {

                    echo "<a href='?finalizar={$row['id']}'
                             onclick='return confirmarExclusao()'>
                             Concluir
                           </a>";

                } else {

                    echo "Apenas visualização";
                }

                echo "</td>
                    </tr>";
            }

            ?>

        </table>

        <?php

        if (
            isset($_GET['finalizar']) &&
            $_SESSION['usuario_tipo'] != 'professor'
        ) {

            $id = $_GET['finalizar'];
            $data_fim = date("Y-m-d");

            $conn->query("UPDATE manutencoes
                          SET status='concluida',
                              data_fim='$data_fim'
                          WHERE id=$id");

            header("Location: manutencoes.php");
            exit();
        }

        ?>

    </div>

</div>

</body>
</html>