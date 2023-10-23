<?php
session_start();
include('funcoes.php');
$usuario_voltou_index = $_GET['voltar'];
$email_do_usuario = $_SESSION['email'];
if (!empty($usuario_voltou_index)) {
  cria_log("LOG_USUARIO_ENVIOU_CODIGO_PARA_EMAIL_MAS_DESISTIU", $email_do_usuario);
  unset($_SESSION['email']);
  unset($_SESSION['id']);
  session_destroy();

  header("Location: index.html");
  exit;
}

cria_log("LOG_USUARIO_DESLOGOU_COM_SUCESSO", $email_do_usuario);
unset($_SESSION['email']);
unset($_SESSION['id']);
session_destroy();

header("Location: index.html?ud"); // Redirecionar para a página de login após sair
