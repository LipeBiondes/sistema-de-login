<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Trocar Senha</title>
  <link rel="stylesheet" href="style.css">
  <script src="bcrypt.js"></script>
</head>

<body>

  <?php
  $email = $_GET["email"] ?? "";
  $codigo = $_GET["codigo"] ?? "";
  $error_message = "";

  if (empty($email) || empty($codigo)) {
    $email = $_POST["email"];
    $codigo = $_POST["codigo"];
    if (empty($email) || empty($codigo)) {
      header("Location: recuperar_senha.php?error=Ocorreu um erro ao tentar recuperar a senha. Por favor, tente novamente.");
    }
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nova_senha = $_POST["nova-senha"];

    include("conexao.php");

    // Verifique se o usuário existe no banco de dados
    $query = "SELECT * FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      $error_message = "Usuário não registrado. Por favor, verifique o e-mail fornecido.";
      header("Location: index.html?error=" . $error_message);
    } else {
      // O e-mail foi encontrado, continue com a verificação do código
      $row = $result->fetch_assoc();
      $codigoRecuperacao = $row["codigo_recuperacao"];

      if (password_verify($codigo, $codigoRecuperacao)) {
        // Senha válida, criptografe-a
        $senhaCriptografada = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualize a senha e limpe o código de recuperação
        $query = "UPDATE usuario SET senha = ?, codigo_recuperacao = NULL WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $senhaCriptografada, $email);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
          // Senha alterada com sucesso, redirecione o usuário
          header("Location: index.html?email=" . $email);
        } else {
          $error_message = "Ocorreu um erro ao alterar a senha. Tente novamente.";
        }
      } else {
        $error_message = "Código de recuperação inválido. Tente novamente.";
      }
    }
  }
  ?>

  <div class="container">
    <h1>Trocar Senha</h1>
    <form id="reset-password-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return validarSenha()">
      <label for="nova-senha">Nova Senha:</label>
      <input type="password" id="nova-senha" name="nova-senha" required>
      <label for="confirmar-senha">Confirmar Senha:</label>
      <input type="password" id="confirmar-senha" required>
      <input type="hidden" name="email" value="<?= $email; ?>">
      <input type="hidden" name="codigo" value="<?= $codigo; ?>">
      <button type="submit">Trocar Senha</button>
    </form>
    <div id="error-message" class="error-message">
      <?php
      if (!empty($error_message)) {
        echo $error_message;
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