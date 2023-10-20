<?php
$mensagem_de_erro = $_GET['error'] ?? '';
$email_do_usuario = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Confirmar Código de Recuperação</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
  if ($email_do_usuario == '') {
    header("Location: recuperar_senha.php?error" . $mensagem_de_erro);
    exit();
  }
  ?>
  <div class="container">
    <h1>Confirmar Código de Recuperação</h1>
    <form id="confirm-form" action="validar_codigo.php" method="get">
      <label for="codigo">Código de Recuperação:</label>
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