<?php
// Conectar ao banco de dados
$conn = new mysqli("localhost", "root", "", "sistema_de_login");

if ($conn->connect_error) {
  header("Location: index.php?erro=Estamos com problemas técnicos, tente novamente mais tarde.");
  die("Conexão falhou: " . $conn->connect_error);
}
