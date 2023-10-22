<?php
include("verificar_sessao.php");
$message = $_GET['message'] ?? "";
$email_usuario = $_SESSION['email'];
$mostrar_ativar_dois_fatores = true;
try {
  include("conexao.php");
  $query = $conn->prepare("SELECT * FROM usuario WHERE email = ?");
  $query->bind_param("s", $email_usuario);
  $query->execute();
  $dados_usuario = $query->get_result();
  $dados_usuario = $dados_usuario->fetch_assoc();

  if ($dados_usuario['verificacao_dois_fatores'] == "sim") {
    $mostrar_ativar_dois_fatores = false;
  } else {
    $mostrar_ativar_dois_fatores = true;
  }
} catch (Exception $e) {
  header("Location: index.html?ept");
  session_destroy();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Configurações</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">
    <h1>Configurações</h1>

    <!-- Botão para alterar a senha -->
    <form action="alterar_senha.php" method="get">
      <button id="configuracoes-alterar-senha" type="submit">Alterar Senha</button>
    </form>

    <?php

    if ($mostrar_ativar_dois_fatores) {
      // <!-- Botão para ativar autenticação de dois fatores -->
      echo "
      <form action=\"ativar_dois_fatores.php\" method=\"post\">
        <input type=\"email\" name=\"email\" style=\"display: none;\" value=\"$email_usuario\">
        <button type=\"submit\" id=\"configuracoes-ativar-fatores\">Ativar Autenticação de Dois Fatores</button>
      </form>";
    } else {
      // <!-- Botão para desativar autenticação de dois fatores -->
      echo "
      <form action=\"desativar_dois_fatores.php\" method=\"post\">
        <input type=\"email\" name=\"email\" style=\"display: none;\" value=\"$email_usuario\">
        <button type=\"submit\" id=\"configuracoes-ativar-fatores\">Desativar Autenticação de Dois Fatores</button>
      </form>";
    }

    ?>
    <div id="success-message" class="success-message">
      <?php
      if (isset($message)) {
        echo $message;
      }
      ?>
    </div>
    <a href="home.php">Voltar</a>
  </div>
</body>

</html>