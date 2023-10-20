<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Cadastro</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <?php
  // Iniciar a sessão
  session_start();

  $msg = ""; // Inicialize a mensagem
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    include("conexao.php");

    // Verificar se o email já está cadastrado
    $check_query = "SELECT id FROM usuario WHERE email = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
      $msg = "Esse email já está cadastrado.";
    } else {
      // Criptografar a senha
      $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

      // Inserir os dados na tabela de usuários
      $stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $nome, $email, $senhaCriptografada);

      if ($stmt->execute()) {
        // Procurar o usuário no banco
        $stmt = $conn->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
          $_SESSION["email"] = $email;
          $_SESSION["nome"] = $nome;

          // Redireciona para a página de login após o cadastro e inicia a sessão
          header("Location: home.php?nome=" . $nome);
          exit();
        }
      } else {
        $msg = "Erro ao cadastrar: " . $conn->error;

        unset($_SESSION['email']);
        unset($_SESSION['password']);
        session_destroy();
      }

      $stmt->close();
    }

    $check_stmt->close();
    $conn->close();
  }
  ?>

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