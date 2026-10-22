<?php
// Sai da conta do usuário logado
session_start();
session_destroy();
header("Location: ../Index.php");
exit;
?>