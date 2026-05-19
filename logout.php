<?php
session_start();

// remove todas as variáveis de sessão
session_unset();

// destrói a sessão
session_destroy();

// garante que não dá pra voltar com "voltar do navegador"
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// redireciona para login
header("Location: login.php");
exit();
?>