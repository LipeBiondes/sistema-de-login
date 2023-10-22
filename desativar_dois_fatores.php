<?php
include("verificar_sessao.php");
include("conexao.php");

$email_do_usuario = $_SESSION["email"];

$query = "UPDATE usuario SET verificacao_dois_fatores = 'nao' WHERE email = '$email_do_usuario' ";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  $messagem = "Não conseguimos desativar a verificação de dois fatores.";
  header("Location: configuracoes.php?message=" . $messagem);
} else {
  // O código inserido pelo usuário é válido então redireciono o usuário para a página configuracoes
  $messagem = "Desativação de dois fatores realizada com sucesso.";
  header("Location: configuracoes.php?message=" . $messagem);
  $stmt->close();
  $conn->close();
  exit();
}
