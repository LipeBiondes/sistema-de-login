<?php
// Iniciar a sessão
session_start();

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

    $data = "LOG 8: O USUÁRIO " . $email_do_usuario . " TENTOU CADASTRAR UM EMAIL EXISTENTE AS " . date('d/m/Y H:i:s', time());
    $data = $data . PHP_EOL;
    $file = fopen("log.txt", "a"); // Abre o arquivo "arquivo.txt" para escrita (se não existir, ele será criado)        

    if ($file) {
      fwrite($file, $data);
      fclose($file); // Fecha o arquivo após a escrita
    }

    $msg = "Esse email já está cadastrado.";
  } else {
    // Criptografar a senha
    $senha_criptografada = password_hash($senha_do_usuario, PASSWORD_DEFAULT);

    // Inserir os dados na tabela de usuários
    $stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome_do_usuario, $email_do_usuario, $senha_criptografada);

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

        // Redireciona para a página de login após o cadastro e inicia a sessão
        session_write_close();
        header("Location: home.php");
        exit();
      }
    } else {

      $data = "LOG 9: O USUÁRIO " . $email_do_usuario . " TENTOU CADASTRAR UM EMAIL EXISTENTE AS " . date('d/m/Y H:i:s', time());
      $data = $data . PHP_EOL;
      $file = fopen("log.txt", "a"); // Abre o arquivo "arquivo.txt" para escrita (se não existir, ele será criado)        

      if ($file) {
        fwrite($file, $data);
        fclose($file); // Fecha o arquivo após a escrita
      }

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

      <p id="error-message"><?= $msg ?></p>

      <button type="submit">Salvar</button>
    </form>
    <a href="index.html">Voltar</a>
  </div>
  <script>
    document.getElementById("signup-form").addEventListener("submit", function(e) {
      var senha = document.getElementById("senha").value;
      var confirmarSenha = document.getElementById("confirmar-senha").value;
      var errorMessage = document.getElementById("error-message");

      // Expressão regular para validar a senha
      var senhaRegex = /^(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

      if (senha !== confirmarSenha) {
        errorMessage.textContent = "As senhas não coincidem.";
        errorMessage.style.display = "block"; // Exibir a mensagem de erro
        e.preventDefault();
      } else {
        if (!senha.match(senhaRegex)) {
          errorMessage.textContent = "A senha deve conter no mínimo 8 caracteres, incluindo pelo menos uma letra maiúscula e uma letra minúscula.";
          errorMessage.style.display = "block"; // Exibir a mensagem de erro
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