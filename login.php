<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email_do_usuario = $_POST["username"];
  $senha_do_usuario = $_POST["password"];
  try {
    include("conexao.php");

    // Procurar o usuário no banco
    $stmt = $conn->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email_do_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      $nome_do_usuario = $row["nome"];
      $senha_criptografada = $row["senha"];
      if (password_verify($senha_do_usuario, $senha_criptografada)) {
        $_SESSION["email"] = $email_do_usuario;
        $_SESSION["password"] = $senha_criptografada;
        header("Location: home.php?nome=" .  $nome_do_usuario);
      }
    } else {
      unset($_SESSION['email']);
      header("Location: index.html?error=Usuário não encontrado ou senha incorreta");
    }
  } catch (Exception $e) {
    header("Location: index.html?error=Estamos com problemas técnicos, tente novamente mais tarde.");
    session_destroy();
  }
}
