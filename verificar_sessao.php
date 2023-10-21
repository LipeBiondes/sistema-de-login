<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário não está logado
if ((!isset($_SESSION["email"]) == true) && (!isset($_SESSION["password"]) == true)) {
  unset($_SESSION['email']);
  unset($_SESSION['password']);

  header("Location: index.html?une");
}
