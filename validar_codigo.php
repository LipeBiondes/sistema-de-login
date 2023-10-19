<?php
$error_message = ""; // Inicialize a mensagem de erro como uma string vazia

$codigo = $_GET["codigo"];
$email = $_GET['email'];

// Conecte-se ao banco de dados
include("conexao.php");

// Verifique se o usuario existe no banco de dados
$query = "SELECT codigo_recuperacao FROM usuario WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  // O usuario não foi encontrado no banco de dados
  header("Location: recuperar_senha.php?error=Usuario não registrado. Por favor, verifique o e-mail fornecido.");
} else {
  // O e-mail foi encontrado, continue com a verificação do código
  $row = $result->fetch_assoc();
  $codigoRecuperacao = $row["codigo_recuperacao"];

  if (password_verify($codigo, $codigoRecuperacao)) {
    // O código inserido pelo usuário é válido então redireciono o usuário para a página de troca de senha com o email e o codigo na URL
    header("Location: trocar_senha.php?email=" . $email . "&codigo=" . $codigo);
    exit();
  } else {
    // O código inserido é inválido, defina a mensagem de erro
    header("Location: confirmar_codigo.php?error=Código de recuperação inválido. Tente novamente.&email=" . $email);
  }
}

$stmt->close();
$conn->close();
