<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email_do_usuario = $_POST["email"];
  include("funcoes.php");
  enviar_email_codigo_confirmacao($email_do_usuario);
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
  <?php
  $error_message = $_GET['error'] ?? '';
  ?>
  <div class="container">
    <h1>Recuperar Senha</h1>

    <form id="recover-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
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