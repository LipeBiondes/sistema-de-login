<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário não está logado
if ((!isset($_SESSION["email"]) == true) && (!isset($_SESSION["id"]) == true)) {
  unset($_SESSION['email']);
  unset($_SESSION['id']);
  session_write_close();
  header("Location: index.html?une");
}
session_write_close();
