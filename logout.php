<?php
session_start();

$usuario_voltou_index = $_GET['voltar'];

if (!empty($usuario_voltou_index)) {
  unset($_SESSION['email']);
  unset($_SESSION['password']);
  session_destroy();

  header("Location: index.html");
  exit;
}
unset($_SESSION['email']);
unset($_SESSION['password']);
session_destroy();

header("Location: index.html?ud"); // Redirecionar para a página de login após sair
