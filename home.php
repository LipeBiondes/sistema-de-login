<?php
include("verificar_sessao.php");
$nome_usuario = "";
$messagem = "";
try {
  $id_usuario = $_SESSION["id"];
  include("conexao.php");
  $query = $conn->prepare("SELECT * FROM usuario WHERE id = ?");
  $query->bind_param("s", $id_usuario);
  $query->execute();
  $resultado = $query->get_result();
  if ($resultado) {
    $dados_usuario = $resultado->fetch_assoc();
    $nome_usuario = $dados_usuario["nome"];

    if ($dados_usuario["codigo_recuperacao"]) {
      $messagem = "Parece que alguém tentou recuperar sua senha. Clique aqui para alterar a sua senha.";
    }
  } else {
    header("Location: index.html?une");
    session_destroy();
  }
} catch (Exception $e) {
  header("Location: index.html?ept");
  $stmt->close();
  $conn->close();
  session_destroy();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Página Inicial</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="home-container">
    <h1 class="home-title">Bem-vindo, <?= $nome_usuario; ?>!</h1>
    <p>Esta é a sua página de início.</p>
    <a class="home-link" href="configuracoes.php">Configurações</a>
    <form method="post" action="logout.php" class="home-form">
      <button class="home-button" type="submit" name="sair">Sair</button>
    </form>
    <div id="danger-message" class="danger-message">
      <?php
      if (!empty($messagem)) {
        echo "<a id=\"danger-message\" href='alterar_senha.php'>$messagem</a>";
      }
      ?>
    </div>
  </div>
</body>

</html>