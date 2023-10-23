<?php
session_start();
include("funcoes.php");
$mensagem_de_erro = $_GET['message'] ?? '';
$email_do_usuario = $_SESSION['email'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $codigo_recuperacao_usuario = $_POST['codigo'];
  $email_do_usuario = $_POST['email'];
  // Certifique-se de que a conexão com o banco de dados seja estabelecida
  include("conexao.php");

  if ($conn) {
    // Verifique se o usuário existe no banco de dados
    $query = "SELECT * FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email_do_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $dados_usuario = $result->fetch_assoc();

    if ($result->num_rows === 0) {
      cria_log('LOG_USUARIO_TENTOU_RECUPERAR_SENHA_COM_EMAIL_INEXISTENTE', $email_do_usuario);

      // O usuário não foi encontrado no banco de dados
      header("Location: index.php?une=");
    } else {
      // O e-mail foi encontrado, continue com a verificação do código
      $row = $result->fetch_assoc();
      $codigo_recuperado_banco = $dados_usuario["codigo_verificacao_dois_fatores"];

      if (password_verify($codigo_recuperacao_usuario, $codigo_recuperado_banco)) {
        cria_log('LOG_USUARIO_RECUPEROU_SENHA_COM_SUCESSO', $email_do_usuario);

        $query = "UPDATE usuario SET codigo_verificacao_dois_fatores = NULL WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email_do_usuario);
        if ($stmt->execute()) {
          // Código de verificação atualizado com sucesso
          cria_log('LOG_USUARIO_LOGOU_COM_SUCESSO', $email_do_usuario);

          $_SESSION["email"] = $email_do_usuario;
          $_SESSION["id"] = $dados_usuario["id"];
          session_write_close();
          header("Location: home.php");
        } else {
          // Não foi possível atualizar o código de verificação
          header("Location: index.html?ncl");
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
    header("Location: confirmar_codigo_dois_fatores.php?message=" . $mensagem_de_erro);
  }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Confirmar Código de Dois Fatores</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
  if (empty($email_do_usuario)) {
    header("Location: index.html?ene");
  }
  ?>
  <div class="container">
    <h1>Confirmar Código de Dois Fatores</h1>
    <form id="confirm-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
      <label for="codigo">Código enviado:</label>
      <input type="text" id="codigo" name="codigo" required>
      <input type="hidden" name="email" value="<?= $email_do_usuario; ?>">
      <?php
      if (!empty($mensagem_de_erro)) {
        echo '<div class="error-message">' . $mensagem_de_erro . '</div>';
      }
      ?>
      <button type="submit">Verificar Código</button>
    </form>
  </div>
</body>

</html>