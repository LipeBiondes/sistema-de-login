<?php
session_start();

include("funcoes.php");

$error_message = ""; // Inicialize a mensagem de erro como uma string vazia

$codigo_recuperacao_usuario = $_POST["codigo"];
$email_do_usuario = $_SESSION["email"];

try {
  // Conecte-se ao banco de dados
  include("conexao.php");

  // Verifique se o usuario existe no banco de dados
  $query = "SELECT codigo_recuperacao FROM usuario WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email_do_usuario);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {

    cria_log('LOG_USUARIO_TENTOU_RECUPERAR_SENHA_COM_EMAIL_INEXISTENTE', $email_do_usuario);

    // O usuario não foi encontrado no banco de dados
    header("Location: recuperar_senha.php?error=Usuario não registrado. Por favor, verifique o e-mail fornecido.");
  } else {
    // O e-mail foi encontrado, continue com a verificação do código
    $row = $result->fetch_assoc();
    $codigo_recuperado_banco = $row["codigo_recuperacao"];

    if (password_verify($codigo_recuperacao_usuario, $codigo_recuperado_banco)) {

      cria_log('LOG_USUARIO_RECUPEROU_SENHA_COM_SUCESSO', $email_do_usuario);

      // O código inserido pelo usuário é válido então redireciono o usuário para a página de troca de senha com o email e o codigo na URL
      header("Location: trocar_senha.php");
      $stmt->close();
      $conn->close();
      exit();
    } else {

      cria_log('LOG_USUARIO_TENTOU_RECUPERAR_SENHA_CODIGO_INCORRETO', $email_do_usuario);

      // O código inserido é inválido, defina a mensagem de erro
      header("Location: confirmar_codigo.php?error=Código de recuperação inválido. Tente novamente.");
    }
  }
} catch (Exception $e) {
  header("Location: index.html?ept");
}
