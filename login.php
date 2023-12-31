<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

include("funcoes.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email_do_usuario = $_POST["username"];
  $senha_do_usuario = $_POST["password"];
  try {
    include("conexao.php");

    // Procurar o usuário no banco
    $query = $conn->prepare("SELECT * FROM usuario WHERE email = ?");
    $query->bind_param("s", $email_do_usuario);
    $query->execute();
    $resultado = $query->get_result();

    if ($resultado) {
      $dados_usuario = $resultado->fetch_assoc();
      $nome_do_usuario = $dados_usuario["nome"];
      $senha_criptografada = $dados_usuario["senha"];

      if (password_verify($senha_do_usuario, $senha_criptografada)) {

        if ($dados_usuario['verificacao_dois_fatores'] == "sim") {
          $_SESSION["email"] = $email_do_usuario;
          enviar_email_dois_fatores_login($email_do_usuario);
        } else {
          cria_log('LOG_USUARIO_LOGOU_COM_SUCESSO', $email_do_usuario);

          $_SESSION["email"] = $email_do_usuario;
          $_SESSION["id"] = $dados_usuario["id"];
          session_write_close();
          header("Location: home.php");
        }
      } else {

        cria_log('LOG_USUARIO_TENTOU_LOGAR_SENHA_INCORRETA', $email_do_usuario);

        // Senhas não conferem
        unset($_SESSION['email']);
        unset($_SESSION['id']);
        session_destroy();
        header("Location: index.html?ene");
      }
    } else {

      cria_log('LOG_USUARIO_TENTOU_LOGAR_COM_EMAIL_INEXISTENTE', $email_do_usuario);

      // Usuário não encontrado
      unset($_SESSION['email']);
      unset($_SESSION['id']);
      session_destroy();
      header("Location: index.html?ene");
    }
  } catch (Exception $e) {
    header("Location: index.html?ept");
    session_destroy();
  }
}
