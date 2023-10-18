<?php
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
    if (password_verify($password, $row["senha"])) {
      session_start();
      $_SESSION["id"] = $row["id"];
      $_SESSION["nome"] = $row["nome"];
      session_write_close(); // Fechar a sessão para evitar problemas de redirecionamento
      header("Location: home.php?nome=" . urlencode($row["nome"]));
      exit();
    }
  }

  // Se chegou aqui, o login falhou
  header("Location: index.html?error=Usuário não encontrado ou senha incorreta");
  exit();
}
