<?php
session_start();

include("funcoes.php");

$email_do_usuario = $_SESSION["email"] ?? "";
$codigo_do_usuario = $_SESSION["codigo-recuperacao"];
$messagem_de_erro = "";

if (empty($email_do_usuario) || empty($codigo_do_usuario)) {
  header("Location: recuperar_senha.php?ers");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nova_senha = $_POST["nova-senha"];
  $email_do_usuario = $_POST["email"];
  $codigo_do_usuario = $_POST["codigo"];

  try {
    // Conecte-se ao banco de dados
    include("conexao.php");

    // Verifique se o usuário existe no banco de dados
    $query = "SELECT * FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email_do_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      // O e-mail não foi encontrado, redirecione o usuário
      header("Location: index.html?une");
    } else {
      // O e-mail foi encontrado, continue com a verificação do código
      $dados_do_usuario = $result->fetch_assoc();
      $codigo_do_banco = $dados_do_usuario["codigo_recuperacao"];

      if (password_verify($codigo_do_usuario, $codigo_do_banco)) {
        // Senha válida, criptografe-a
        $nova_senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Verificando se a nova senha é igual a senha antiga
        if (password_verify($nova_senha, $dados_do_usuario["senha"])) {
          $messagem_de_erro = "A nova senha não pode ser igual a senha antiga.";
        } else {
          // Atualize a senha e limpe o código de recuperação
          $query = "UPDATE usuario SET senha = ?, codigo_recuperacao = NULL WHERE email = ?";
          $stmt = $conn->prepare($query);
          $stmt->bind_param("ss", $nova_senha_criptografada, $email_do_usuario);
          $stmt->execute();

          if ($stmt->affected_rows > 0) {

            cria_log('LOG9', $email_do_usuario);

            // Senha alterada com sucesso, redirecione o usuário
            unset($_SESSION["email"]);
            session_destroy();
            header("Location: index.html?sas");
          } else {

            cria_log('LOG10', $email_do_usuario);

            $messagem_de_erro = "Ocorreu um erro ao alterar a senha. Tente novamente.";
          }
        }
      } else {

        cria_log('LOG6', $email_do_usuario);

        $messagem_de_erro = "Código de recuperação inválido. Tente novamente.";
      }
    }
  } catch (Exception $e) {

    cria_log('LOG10', $email_do_usuario);

    header("Location: index.html?ept");
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Trocar Senha</title>
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

      <span id="showPassword" onclick="togglePasswordVisibility()">
        <i class="fas fa-eye"></i>
      </span>
      Mostrar Senha

      <input type="hidden" name="email" value="<?= $email_do_usuario; ?>">
      <input type="hidden" name="codigo" value="<?= $codigo_do_usuario; ?>">
      <div id="error-message" class="error-message">
        <?php
        if (!empty($messagem_de_erro)) {
          echo $messagem_de_erro;
        }
        ?>
      </div>

      <button type="submit">Trocar Senha</button>
      <a href="logout.php?voltar=usuario voltou">Voltar</a>
    </form>
  </div>

  <script>
    // Função para mostrar/esconder a senha
    function togglePasswordVisibility() {
      var novaSenhaField = document.getElementById("nova-senha");
      var confirmarSenhaField = document.getElementById("confirmar-senha");
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
  </script>
</body>

</html>