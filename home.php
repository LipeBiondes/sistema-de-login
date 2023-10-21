<?php
include("verificar_sessao.php");
$nome_usuario = "";
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
  </div>
</body>

</html>