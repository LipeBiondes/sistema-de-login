<?php
session_start();
$mensagem_de_erro = $_GET['message'] ?? '';
$email_do_usuario = $_SESSION['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Confirmar Código de Ativação</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
  if ($email_do_usuario == '') {
    $messagem_de_erro = "ocorreu um erro, tente novamente.";
    header("Location: configuracoes.php" . $mensagem_de_erro);
  }
  ?>
  <div class="container">
    <h1>Confirmar Código de Ativação</h1>
    <form id="confirm-form" action="validar_codigo_dois_fatores.php" method="post">
      <label for="codigo">Código de Ativação:</label>
      <input type="text" id="codigo" name="codigo" required>
      <input type="hidden" name="email" value="<?= $email_do_usuario; ?>">
      <?php
      if (!empty($mensagem_de_erro)) {
        echo '<div class="error-message">' . $mensagem_de_erro . '</div>';
      }
      ?>
      <button type="submit">Verificar Código</button>
    </form>
  </div>
</body>

</html>