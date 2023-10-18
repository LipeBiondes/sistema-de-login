<?php
include("verificar_sessao.php");

if (isset($_SESSION['id']) && isset($_SESSION['nome'])) {
  $nomeUsuario = $_SESSION['nome'];
} else {
  // Se o usuário não está logado, redirecione para a página de login
  header("Location: index.html");
  exit();
}

// Se o usuário clicou em "Sair," encerre a sessão
if (isset($_POST['sair'])) {
  session_unset();
  session_destroy();
  header("Location: index.html"); // Redirecionar para a página de login após sair
  exit();
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
    <h1 class="home-title">Bem-vindo, <?php echo $nomeUsuario; ?>!</h1>
    <p>Esta é a sua página de início.</p>
    <a class="home-link" href="configuracoes.php">Configurações</a>
    <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" class="home-form">
      <button class="home-button" type="submit" name="sair">Sair</button>
    </form>
  </div>
</body>

</html>