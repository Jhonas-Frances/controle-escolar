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
    <title>Equipamentos</title>

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
            <strong>Equipamentos</strong>
        </div>
        <?php
        if (isset($_GET['erro'])) {

            if ($_GET['erro'] == 'manutencao') {

                echo "<div class='mensagem-erro'>
                Não é possível excluir este equipamento porque existem manutenções vinculadas.
              </div>";
            }

            if ($_GET['erro'] == 'reserva') {

                echo "<div class='mensagem-erro'>
                Não é possível excluir este equipamento porque existem reservas vinculadas.
              </div>";
            }
        }

        if (isset($_GET['sucesso'])) {

            if ($_GET['sucesso'] == 'excluido') {

                echo "<div class='mensagem-sucesso'>
                Equipamento excluído com sucesso!
              </div>";
            }
        }
        ?>


        <!-- CADASTRO -->
        <div class="card">

            <h3>Cadastrar Equipamento</h3>

            <form method="POST">

                <input type="text" name="codigo" placeholder="Código (ex: PC-01)" required>

                <select name="tipo_id" required>
                    <option value="">Selecione o tipo</option>
                    <?php
                    $tipos = $conn->query("SELECT * FROM tipos_equipamento");
                    while ($t = $tipos->fetch_assoc()) {
                        echo "<option value='{$t['id']}'>{$t['nome']}</option>";
                    }
                    ?>
                </select>

                <select name="local_id" required>
                    <option value="">Selecione o local</option>
                    <?php
                    $locais = $conn->query("SELECT * FROM locais");
                    while ($l = $locais->fetch_assoc()) {
                        echo "<option value='{$l['id']}'>{$l['nome']}</option>";
                    }
                    ?>
                </select>

                <input type="text" name="descricao" placeholder="Descrição" required>

                <button type="submit" name="cadastrar">Cadastrar</button>
            </form>

            <?php
            if (isset($_POST['cadastrar'])) {

                $codigo = $_POST['codigo'];
                $tipo_id = $_POST['tipo_id'];
                $local_id = $_POST['local_id'];
                $descricao = $_POST['descricao'];

                $sql = "INSERT INTO equipamentos
                (codigo, tipo_id, local_id, descricao, status)
                VALUES
                ('$codigo', $tipo_id, $local_id, '$descricao', 'disponivel')";

                if ($conn->query($sql)) {

                    echo "<div class='mensagem-sucesso'>
                    Equipamento cadastrado com sucesso!
                  </div>";

                } else {

                    echo "<div class='mensagem-erro'>
                    Erro ao cadastrar equipamento!
                  </div>";
                }
            }
            ?>

        </div>

        <!-- FILTRO -->
        <div class="card">

            <h3>Filtrar por Local</h3>

            <form method="GET">
                <select name="filtro_local" onchange="this.form.submit()">
                    <option value="">Todos os locais</option>

                    <?php
                    $locais = $conn->query("SELECT * FROM locais");
                    while ($l = $locais->fetch_assoc()) {

                        $selected = (isset($_GET['filtro_local']) && $_GET['filtro_local'] == $l['id']) ? "selected" : "";

                        echo "<option value='{$l['id']}' $selected>{$l['nome']}</option>";
                    }
                    ?>
                </select>
            </form>

        </div>

        <!-- LISTAGEM -->
        <div class="card">

            <h3>Lista de Equipamentos</h3>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Local</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>

                <?php
                $filtro = "";

                if (isset($_GET['filtro_local']) && !empty($_GET['filtro_local'])) {
                    $local_id = $_GET['filtro_local'];
                    $filtro = "WHERE e.local_id = $local_id";
                }

                $sql = "SELECT e.*, t.nome AS tipo, l.nome AS local
                    FROM equipamentos e
                    LEFT JOIN tipos_equipamento t ON e.tipo_id = t.id
                    LEFT JOIN locais l ON e.local_id = l.id
                    $filtro";

                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {

                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['codigo']}</td>
                        <td>{$row['tipo']}</td>
                        <td>{$row['local']}</td>
                        <td>";

                    if ($row['status'] == 'disponivel') {
                        echo "<span class='status-disponivel'>Disponível</span>";
                    } else {
                        echo "<span class='status-emprestado'>Em uso</span>";
                    }

                    echo "</td>
                        <td>
                            <a href='?excluir={$row['id']}'
                               onclick='return confirmarExclusao()'>
                               Excluir
                            </a>
                        </td>
                      </tr>";
                }
                ?>

            </table>

            <?php
            if (isset($_GET['excluir'])) {

                $id = (int) $_GET['excluir'];

                // Verifica manutenções
                $manutencoes = $conn->query("
        SELECT COUNT(*) AS total
        FROM manutencoes
        WHERE equipamento_id = $id
    ")->fetch_assoc();
                if ($manutencoes['total'] > 0) {

                    echo "<script>
            window.location='equipamentos.php?erro=manutencao';
          </script>";
                    exit();
                }

                // Verifica reservas
                $reservas = $conn->query("
    SELECT COUNT(*) AS total
    FROM reservas
    WHERE equipamento_id = $id
")->fetch_assoc();

                if ($reservas['total'] > 0) {

                    echo "<script>
            window.location='equipamentos.php?erro=reserva';
          </script>";
                    exit();
                }

                // Exclui equipamento
                $conn->query("DELETE FROM equipamentos WHERE id = $id");

                echo "<script>
        window.location='equipamentos.php?sucesso=excluido';
      </script>";
                exit();
            }
            ?>
        </div>

    </div>

</body>

</html>