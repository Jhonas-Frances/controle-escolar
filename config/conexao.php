<?php

// Configurações de acesso ao banco de dados
$host = "localhost";
$user = "root";
$pass = "";
$db = "controle_escolar";

// Cria a conexão com o banco de dados MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Verifica se ocorreu algum erro durante a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

?>