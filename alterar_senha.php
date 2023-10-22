<?php
include("verificar_sessao.php");

include("funcoes.php");

$messagem_de_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nova_senha = $_POST["nova-senha"];

  // Certifique-se de que a conexão com o banco de dados seja estabelecida
  include("conexao.php");

  $email_do_usuario = $_SESSION["email"];
  $id_do_usuario = $_SESSION["id"];
  $nova_senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);

  try {
    $query = $conn->prepare("SELECT * FROM usuario WHERE id = ? ");
    $query->bind_param("i", $id_do_usuario);
    $query->execute();
    $result = $query->get_result();
    $dados_do_usuario = $result->fetch_assoc();

    if (password_verify($nova_senha, $dados_do_usuario["senha"])) {
      $messagem_de_erro = "A nova senha não pode ser igual a senha antiga.";
    } else {
      // Atualize a senha no banco de dados
      $query = $conn->prepare("UPDATE usuario SET senha = ?, codigo_recuperacao = NULL WHERE id = ?");
      $query->bind_param("si", $nova_senha_criptografada, $id_do_usuario);

      if ($query->execute()) {

        cria_log('LOG9', $email_do_usuario);

        // Redirecione para a página inicial após a atualização bem-sucedida
        $_SESSION["id"] = $id_do_usuario;
        $_SESSION["email"] = $email_do_usuario;
        header("Location: home.php");
        session_write_close();
      } else {

        cria_log('LOG10', $email_do_usuario);

        $messagem_de_erro = "Não foi possível alterar a senha. Por favor, tente novamente.";
      }
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body>

  <div class="container">
    <h1>Trocar Senha</h1>
    <form id="reset-password-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return validarSenha()">
      <label for="nova-senha">Nova Senha:</label>
      <input type="password" id="nova-senha" name="nova-senha" required>
      <label for="confirmar-senha">Confirmar Senha:</label>
      <input type="password" id="confirmar-senha" required>

      <span id="showPassword" onclick="togglePasswordVisibility('nova-senha', 'confirmar-senha')">
        <i class="fas fa-eye" style="cursor:pointer;"></i>
      </span>
      Mostrar senha

      <div id="error-message" class="error-message">
        <?php
        if (!empty($messagem_de_erro)) {
          echo $messagem_de_erro;
        }
        ?>
      </div>
      <button type="submit">Trocar Senha</button>
      <a href="configuracoes.php">Voltar</a>
    </form>
  </div>

  <script>
    function togglePasswordVisibility(fieldId1, fieldId2) {
      var novaSenhaField = document.getElementById(fieldId1);
      var confirmarSenhaField = document.getElementById(fieldId2);
      var showPasswordIcon = document.querySelector("#showPassword i");

      if (novaSenhaField.type === "password") {
        novaSenhaField.type = "text";
        confirmarSenhaField.type = "text";
        showPasswordIcon.classList.remove("fa-eye");
        showPasswordIcon.classList.add("fa-eye-slash");
      } else {
        novaSenhaField.type = "password";
        confirmarSenhaField.type = "password";
        showPasswordIcon.classList.remove("fa-eye-slash");
        showPasswordIcon.classList.add("fa-eye");
      }
    }


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

    //funcao  de settimeout
    function hideErrorMessage() {
      var errorMessage = document.getElementById("error-message");
      if (errorMessage) {
        errorMessage.style.display = "none";
      }

    }


    document.addEventListener("DOMContentLoaded", function() {
      setTimeout(hideErrorMessage, 5000);

    });
  </script>
</body>

</html>