<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../sistema-de-login/PHPMailer/src/Exception.php';
require '../sistema-de-login/PHPMailer/src/PHPMailer.php';
require '../sistema-de-login/PHPMailer/src/SMTP.php';

$email = $_GET["email"];

// Conecte-se ao banco de dados 
include("conexao.php");

// Verifique se o e-mail existe no banco de dados
$query = "SELECT * FROM usuario WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  // O e-mail não foi encontrado no banco de dados, exiba uma mensagem de erro
  header("Location: recuperar_senha.php?error=E-mail não registrado. Por favor, verifique o e-mail fornecido.");
} else {
  // Gerando um código de recuperação único
  $code = bin2hex(random_bytes(2));

  // Criptografando o código de recuperação antes de armazená-lo no banco de dados
  $hashedCode = password_hash($code, PASSWORD_DEFAULT);

  // Inserindo o código criptografado no banco de dados junto com o e-mail do usuário
  $query = "UPDATE usuario SET codigo_recuperacao = ? WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $hashedCode, $email);
  $stmt->execute();

  // Verifique se o código de recuperação foi inserido com sucesso
  if ($stmt->affected_rows > 0) {

    // Enviando o código para o e-mail do usuário

    $mail = new PHPMailer(true);

    try {
      //Server settings
      $mail->isSMTP();
      $mail->Host       = 'smtp.gmail.com';
      $mail->SMTPAuth   = true;
      $mail->Username   = 'turmabes2020@gmail.com';
      $mail->Password   = 'mpuo gjpn mxmp wztb';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port       = 465;

      //Recipients
      $mail->setFrom('turmabes2020@gmail.com', 'Turma de Bes 2020');
      $mail->addAddress($email);     //Add a recipient


      //Content
      $mail->isHTML(true);
      // Assunto e corpo do e-mail
      $mail->Subject = 'Codigo de Recuperacao de Senha';
      $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
        </head>
        <body>
          <h1 style="color: #007bff;">Recuperação de Senha</h1>
          <p>Olá!</p>
          <p>Recebemos uma solicitação de recuperação de senha para a sua conta. Use o código abaixo para redefinir sua senha:</p>
          <div style="background-color: #f5f5f5; padding: 10px; border-radius: 5px; margin: 20px 0;">
            <h2 style="color: #007bff;">Código de Recuperação:</h2>
            <p style="font-size: 24px;">' . $code . '</p>
          </div>
          <p>Se você não solicitou uma recuperação de senha, você pode ignorar este e-mail com segurança.</p>
          <p>Atenciosamente,</p>
          <p>Sua equipe de suporte</p>
        </body>
        </html>';

      $mail->send();

      // Redirecione o usuário para a página de confirmação
      header("Location: confirmar_codigo.php?email=" . $email);
      exit();
    } catch (Exception $e) {
      header("Location: recuperar_senha.php?error=Erro ao enviar o e-mail:" . $mail->ErrorInfo);
    }
  } else {
    // Se a atualização não teve êxito, exiba uma mensagem de erro
    header("Location: recuperar_senha.php?error=Ocorreu um erro ao gerar o código de recuperação. Tente novamente.");
  }
}

$stmt->close();
$conn->close();
