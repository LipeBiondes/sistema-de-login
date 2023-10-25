<?php
// Iniciar a sessão
session_start();

include("funcoes.php");

$msg = ""; // Inicialize a mensagem
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nome_do_usuario = $_POST["nome"];
  $email_do_usuario = $_POST["email"];
  $senha_do_usuario = $_POST["senha"];

  include("conexao.php");

  // Verificar se o email já está cadastrado
  $check_query = "SELECT id FROM usuario WHERE email = ?";
  $check_stmt = $conn->prepare($check_query);
  $check_stmt->bind_param("s", $email_do_usuario);
  $check_stmt->execute();
  $check_result = $check_stmt->get_result();

  if ($check_result->num_rows > 0) {

    cria_log('LOG_USUARIO_TENTOU_CADASTRAR_EMAIL_EXISTENTE', $email_do_usuario);

    $msg = "Esse email já está cadastrado.";
  } else {
    // Criptografar a senha
    $senha_criptografada = password_hash($senha_do_usuario, PASSWORD_DEFAULT);

    $dois_fatores = "nao";
    // Inserir os dados na tabela de usuários
    $stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha,verificacao_dois_fatores) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome_do_usuario, $email_do_usuario, $senha_criptografada, $dois_fatores);

    if ($stmt->execute()) {
      // Procurar o usuário no banco
      $stmt = $conn->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
      $stmt->bind_param("s", $email_do_usuario);
      $stmt->execute();
      $result = $stmt->get_result();
      $dados_salvos = $result->fetch_assoc();
      $id_usuario = $dados_salvos["id"];
      if ($result->num_rows == 1) {
        $_SESSION["email"] = $email_do_usuario;
        $_SESSION["id"] = $id_usuario;

        cria_log('LOG_USUARIO_CADASTROU_COM_SUCESSO', $email_do_usuario);

        // Redireciona para a página de login após o cadastro e inicia a sessão
        session_write_close();
        header("Location: home.php");
        exit();
      }
    } else {

      cria_log('LOG_USUARIO_TENTOU_CADASTRAR_MAS_FALHA_AO_INSERIR_NO_BANCO', $email_do_usuario);

      $msg = "Erro ao cadastrar: Por favor tente novamente";

      unset($_SESSION['email']);
      unset($_SESSION['id']);
      session_destroy();
    }

    $stmt->close();
  }

  $check_stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Cadastro</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body>

  <div class="container">
    <h1>Cadastro</h1>
    <form id="signup-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
      <label for="nome">Nome:</label>
      <input type="text" id="nome" name="nome" required />

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required />

      <label for="senha">Senha:</label>
      <input type="password" id="senha" name="senha" required />

      <label for="confirmar-senha">Confirmar Senha:</label>
      <input type="password" id="confirmar-senha" name="confirmar-senha" required />

      <span id="showPassword" onclick="togglePasswordVisibility()" style="cursor:pointer;">
        <i class="fas fa-eye"></i>
      </span>
      Mostar Senha


      <p id="error-message"><?= $msg ?></p>
      <label for="checkbox" id="label-termos-condicoes">
        Eu aceito os
        <a id="a-termos-condicoes" href="termos_e_condicoes.html" target="_blank">termos e condições</a>
        <input type="checkbox" id="input-termos-condicoes" name="checkbox" required />
      </label>

      <button type="submit">Salvar</button>
    </form>
    <a href="index.html">Voltar</a>
  </div>
  <script>
    function hideMessage() {
      setTimeout(function() {
        document.getElementById("error-message").style.display = "none";
      }, 5000); // 5 segundos
    }


    // Função para mostrar/esconder a senha
    function togglePasswordVisibility() {
      var senhaField = document.getElementById("senha");
      var confirmarSenhaField = document.getElementById("confirmar-senha");
      var showPasswordIcon = document.querySelector("#showPassword i");

      if (senhaField.type === "password") {
        senhaField.type = "text";
        confirmarSenhaField.type = "text";
        showPasswordIcon.classList.remove("fa-eye");
        showPasswordIcon.classList.add("fa-eye-slash");
      } else {
        senhaField.type = "password";
        confirmarSenhaField.type = "password";
        showPasswordIcon.classList.remove("fa-eye-slash");
        showPasswordIcon.classList.add("fa-eye");
      }
    }


    document.getElementById("signup-form").addEventListener("submit", function(e) {
      var senha = document.getElementById("senha").value;
      var confirmarSenha = document.getElementById("confirmar-senha").value;
      var errorMessage = document.getElementById("error-message");

      // Expressão regular para validar a senha
      var senhaRegex = /^(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

      if (senha !== confirmarSenha) {
        errorMessage.textContent = "As senhas não coincidem.";
        errorMessage.style.display = "block"; // Exibir a mensagem de erro
        hideMessage();
        e.preventDefault();
      } else {
        if (!senha.match(senhaRegex)) {
          errorMessage.textContent = "A senha deve conter no mínimo 8 caracteres, incluindo pelo menos uma letra maiúscula e uma letra minúscula.";
          errorMessage.style.display = "block"; // Exibir a mensagem de erro
          hideMessage();
          e.preventDefault();
        } else {
          errorMessage.textContent = ""; // Limpar a mensagem de erro
          errorMessage.style.display = "none"; // Ocultar a mensagem de erro
        }
      }
    });
  </script>
</body>

</html>