<?php
include("verificar_sessao.php");

$messagem_de_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nova_senha = $_POST["nova-senha"];

  // Certifique-se de que a conexão com o banco de dados seja estabelecida
  include("conexao.php");

  $email_do_usuario = $_SESSION["email"];
  $id_do_usuario = $_SESSION["id"];
  $nova_senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);

  try {
    // Atualize a senha no banco de dados
    $query = $conn->prepare("UPDATE usuario SET senha = ?, codigo_recuperacao = NULL WHERE id = ?");
    $query->bind_param("si", $nova_senha_criptografada, $id_do_usuario);

    if ($query->execute()) {
      // Redirecione para a página inicial após a atualização bem-sucedida
      $_SESSION["id"] = $id_do_usuario;
      $_SESSION["email"] = $email_do_usuario;
      header("Location: home.php");
      session_write_close();
    } else {
      $messagem_de_erro = "Não foi possível alterar a senha. Por favor, tente novamente.";
    }
  } catch (Exception $e) {
    // Em caso de exceção, redirecione para a página de erro e destrua a sessão
    header("Location: index.html?ept");
    session_destroy();
  }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Alterar Senha</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <div class="container">
    <h1>Trocar Senha</h1>
    <form id="reset-password-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return validarSenha()">
      <label for="nova-senha">Nova Senha:</label>
      <input type="password" id="nova-senha" name="nova-senha" required>
      <label for="confirmar-senha">Confirmar Senha:</label>
      <input type="password" id="confirmar-senha" required>
      <button type="submit">Trocar Senha</button>
    </form>
    <div id="error-message" class="error-message">
      <?php
      if (!empty($messagem_de_erro)) {
        echo $messagem_de_erro;
      }
      ?>
    </div>
  </div>

  <script>
    function validarSenha() {
      var novaSenha = document.getElementById('nova-senha').value;
      var confirmarSenha = document.getElementById('confirmar-senha').value;

      if (novaSenha !== confirmarSenha) {
        var errorMessage = "As senhas não coincidem. Por favor, verifique.";
        document.getElementById('error-message').textContent = errorMessage;
        document.getElementById('error-message').style.display = 'block';
        return false;
      }

      // Use uma expressão regular para verificar se a senha atende aos critérios
      var regex = /^(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
      if (!regex.test(novaSenha)) {
        var errorMessage = "A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula e uma letra minúscula.";
        document.getElementById('error-message').textContent = errorMessage;
        document.getElementById('error-message').style.display = 'block';
        return false;
      }
      return true;
    }
  </script>
</body>

</html>