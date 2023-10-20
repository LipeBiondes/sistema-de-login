<?php
include("verificar_sessao.php");
$nome_usuario = $_GET["nome"] ?? "Usuario";
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
    <h1 class="home-title">Bem-vindo, <?php echo $nome_usuario; ?>!</h1>
    <p>Esta é a sua página de início.</p>
    <a class="home-link" href="configuracoes.php">Configurações</a>
    <form method="post" action="logout.php" class="home-form">
      <button class="home-button" type="submit" name="sair">Sair</button>
    </form>
  </div>
</body>

</html>