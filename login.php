<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  include("conexao.php");

  // Procurar o usuário no banco
  $stmt = $conn->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $nome = $row["nome"];
    $senha_encriptografada = $row["senha"];
    if (password_verify($password, $senha_encriptografada)) {
      $_SESSION["email"] = $username;
      $_SESSION["password"] = $senha_encriptografada;
      header("Location: home.php?nome=" .  $nome);
    }
  } else {
    unset($_SESSION['email']);
    header("Location: index.html?error=Usuário não encontrado ou senha incorreta");
  }
}
