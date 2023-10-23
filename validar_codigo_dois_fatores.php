<?php
session_start();

include("funcoes.php");

$error_message = ""; // Inicialize a mensagem de erro como uma string vazia

$codigo_recuperacao_usuario = $_POST["codigo"];
$email_do_usuario = $_SESSION["email"];

// Certifique-se de que a conexão com o banco de dados seja estabelecida
include("conexao.php");

if ($conn) {
  // Verifique se o usuário existe no banco de dados
  $query = "SELECT codigo_verificacao_dois_fatores FROM usuario WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email_do_usuario);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    cria_log('LOG_USUARIO_TENTOU_RECUPERAR_SENHA_COM_EMAIL_INEXISTENTE', $email_do_usuario);

    // O usuário não foi encontrado no banco de dados
    $mensagem_de_erro = "Ocorreu um erro, tente novamente.";
    header("Location: configuracoes.php?message=" . $mensagem_de_erro);
  } else {
    // O e-mail foi encontrado, continue com a verificação do código
    $row = $result->fetch_assoc();
    $codigo_recuperado_banco = $row["codigo_verificacao_dois_fatores"];

    if (password_verify($codigo_recuperacao_usuario, $codigo_recuperado_banco)) {
      cria_log('LOG_USUARIO_RECUPEROU_SENHA_COM_SUCESSO', $email_do_usuario);

      $query = "UPDATE usuario SET verificacao_dois_fatores = 'sim', codigo_verificacao_dois_fatores = NULL WHERE email = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $email_do_usuario);
      $stmt->execute();

      if ($stmt->affected_rows > 0) {
        // A ativação de dois fatores foi bem-sucedida
        $mensagem = "Ativação de dois fatores realizada com sucesso.";
        header("Location: configuracoes.php?message=" . $mensagem);
      } else {
        $mensagem = "Não conseguimos ativar a verificação de dois fatores.";
        header("Location: configuracoes.php?message=" . $mensagem);
      }
      $stmt->close();
      $conn->close();
      exit();
    } else {
      cria_log('LOG_USUARIO_TENTOU_RECUPERAR_SENHA_CODIGO_INCORRETO', $email_do_usuario);

      // O código inserido é inválido, defina a mensagem de erro
      header("Location: confirmar_codigo_dois_fatores.php?message=Código de recuperação inválido. Tente novamente.");
    }
  }
} else {
  $mensagem_de_erro = "Ocorreu um erro, tente novamente.";
  header("Location: configuracoes.php?message=" . $mensagem_de_erro);
}
