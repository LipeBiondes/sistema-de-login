<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST["email"];

  // Conecte-se ao banco de dados 
  include("conexao.php");

  // Verifique se o e-mail existe no banco de dados
  $query = "SELECT * FROM usuario WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    // O e-mail não foi encontrado no banco de dados, exiba uma mensagem de erro
    $error_message = "E-mail não registrado. Por favor, verifique o e-mail fornecido.";
  } else {
    // O e-mail foi encontrado, continue com o processo de recuperação

    // Gerando um código de recuperação único
    $code = bin2hex(random_bytes(8));

    // Criptografando o código de recuperação antes de armazená-lo no banco de dados
    $hashedCode = password_hash($code, PASSWORD_DEFAULT);

    // Inserindo o código criptografado no banco de dados junto com o e-mail do usuário
    $query = "UPDATE usuario SET codigo_recuperacao = ? WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $hashedCode, $email);
    $stmt->execute();

    // Verifique se o código de recuperação foi inserido com sucesso
    if ($stmt->affected_rows > 0) {
      // Enviando o código para o e-mail do usuário

      // Redirecione o usuário para a página de confirmação
      header("Location: confirmacao.php?email=$email");
      exit();
    } else {
      // Se a atualização não teve êxito, exiba uma mensagem de erro
      $error_message = "Ocorreu um erro ao gerar o código de recuperação. Tente novamente.";
    }
  }

  $stmt->close();
  $conn->close();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Recuperar Senha</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="container">
    <h1>Recuperar Senha</h1>

    <form id="recover-form" action="recuperar_senha.php" method="post">
      <label for="email">E-mail:</label>
      <input type="email" id="email" name="email" required />
      <div id="error-message" class="error-message">
        <?php
        if (isset($error_message)) {
          echo $error_message;
        }
        ?>
      </div>
      <button type="submit">Enviar Código de Recuperação</button>
    </form>

    <p>Já possui uma conta? <a href="index.html">Faça o login aqui</a></p>
  </div>

  <script>
    const urlParams = new URLSearchParams(window.location.search)
    const errorMessage = urlParams.get('error')

    if (errorMessage) {
      const errorMessageDiv = document.getElementById('error-message')
      errorMessageDiv.textContent = errorMessage
      errorMessageDiv.style.display = 'block'
    }
  </script>
</body>

</html>