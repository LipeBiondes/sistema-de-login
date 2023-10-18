<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário não está logado
if (!isset($_SESSION['id']) || !isset($_SESSION['nome'])) {
  header("Location: index.html");
  exit();
}
