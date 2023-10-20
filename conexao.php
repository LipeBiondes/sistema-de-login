<?php
// Conectar ao banco de dados
$conn = new mysqli("localhost", "root", "", "sistema_de_login");

if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}
