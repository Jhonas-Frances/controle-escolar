<?php
// inicia sessão só se ainda não iniciou
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /controle_escolar/login.php");
    exit();
}
?>