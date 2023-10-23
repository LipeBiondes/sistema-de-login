<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../sistema-de-login/PHPMailer/src/Exception.php';
require '../sistema-de-login/PHPMailer/src/PHPMailer.php';
require '../sistema-de-login/PHPMailer/src/SMTP.php';

function enviar_email_codigo_confirmacao($email_do_usuario)
{
    try {

        // Conecte-se ao banco de dados 
        include("conexao.php");

        // Verifique se o e-mail existe no banco de dados
        $query = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email_do_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // O e-mail não foi encontrado no banco de dados, exiba uma mensagem de erro
            header("Location: recuperar_senha.php?error=E-mail não registrado. Por favor, verifique o e-mail fornecido.");
        } else {
            // Gerando um código de recuperação único
            $codigo_recuperacao = bin2hex(random_bytes(2));

            // Criptografando o código de recuperação antes de armazená-lo no banco de dados
            $codigo_recuperacao_criptografado = password_hash($codigo_recuperacao, PASSWORD_DEFAULT);

            // Inserindo o código criptografado no banco de dados junto com o e-mail do usuário
            $query = "UPDATE usuario SET codigo_recuperacao = ? WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $codigo_recuperacao_criptografado, $email_do_usuario);
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
                    $mail->addAddress($email_do_usuario);     //Add a recipient


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
              <p style="font-size: 24px;">' . $codigo_recuperacao . '</p>
            </div>
            <p>Se você não solicitou uma recuperação de senha, você pode ignorar este e-mail com segurança.</p>
            <p>Atenciosamente,</p>
            <p>Sua equipe de suporte</p>
          </body>
          </html>';

                    $mail->send();

                    // Redirecione o usuário para a página de confirmação
                    $_SESSION['email'] = $email_do_usuario;
                    header("Location: confirmar_codigo.php");
                    session_write_close();
                    $stmt->close();
                    $conn->close();
                    exit();
                } catch (Exception $e) {
                    session_destroy();
                    header("Location: recuperar_senha.php?error=Erro ao enviar o e-mail:" . $mail->ErrorInfo);
                }
            } else {
                // Se a atualização não teve êxito, exiba uma mensagem de erro
                header("Location: recuperar_senha.php?error=Ocorreu um erro ao gerar o código de recuperação. Tente novamente.");
            }
        }
    } catch (Exception $e) {
        header("Location: index.html?error=Estamos com problemas técnicos, tente novamente mais tarde.");
    }
}

function enviar_email_dois_fatores($email_do_usuario)
{
    try {

        // Conecte-se ao banco de dados 
        include("conexao.php");

        // Verifique se o e-mail existe no banco de dados
        $query = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email_do_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // O e-mail não foi encontrado no banco de dados, exiba uma mensagem de erro
            header("Location: configuracoes.php?message=tivemos um problema ao tentar ativar a verificação de dois fatores.");
        } else {
            // Gerando um código de recuperação único
            $codigo_recuperacao_dois_fatores = bin2hex(random_bytes(2));

            // Criptografando o código de recuperação antes de armazená-lo no banco de dados
            $codigo_recuperacao_dois_fatores_criptografado = password_hash($codigo_recuperacao_dois_fatores, PASSWORD_DEFAULT);

            // Inserindo o código criptografado no banco de dados junto com o e-mail do usuário
            $query = "UPDATE usuario SET codigo_verificacao_dois_fatores = ? WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $codigo_recuperacao_dois_fatores_criptografado, $email_do_usuario);
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
                    $mail->addAddress($email_do_usuario);     //Add a recipient


                    //Content
                    $mail->isHTML(true);
                    // Assunto e corpo do e-mail
                    $mail->Subject = 'Ativacao da Autenticacao de Dois Fatores';
                    $mail->Body = '
          <!DOCTYPE html>
          <html>
          <head>
          </head>
          <body>
            <h1 style="color: #007bff;">Ativação da Autenticação de Dois Fatores</h1>
            <p>Olá!</p>
            <p>Você está a um passo de adicionar uma camada adicional de segurança à sua conta. Use o código de ativação abaixo para configurar a autenticação de dois fatores:</p>
            <div style="background-color: #f5f5f5; padding: 10px; border-radius: 5px; margin: 20px 0;">
              <h2 style="color: #007bff;">Código de Ativação:</h2>
              <p style="font-size: 24px;">' . $codigo_recuperacao_dois_fatores . '</p>
            </div>
            <p>Se você não solicitou a ativação da autenticação de dois fatores, você pode ignorar este e-mail com segurança.</p>
            <p>Atenciosamente,</p>
            <p>Sua equipe de suporte</p>
          </body>
          </html>';

                    $mail->send();

                    // Redirecione o usuário para a página de confirmação
                    $_SESSION["codigo-recuperacao"] = $codigo_recuperacao_dois_fatores;
                    header("Location: confirmar_codigo_dois_fatores.php");
                    session_write_close();
                    $stmt->close();
                    $conn->close();
                    exit();
                } catch (Exception $e) {
                    header("Location: configuracoes.php?message=Erro ao enviar o e-mail:" . $mail->ErrorInfo);
                }
            } else {
                // Se a atualização não teve êxito, exiba uma mensagem de erro
                header("Location: configuracoes.php?message=tivemos um problema ao tentar ativar a verificação de dois fatores.");
            }
        }
    } catch (Exception $e) {
        header("Location: configuracoes.php?error=Estamos com problemas técnicos, tente novamente mais tarde.");
    }
}


function enviar_email_dois_fatores_login($email_do_usuario)
{
    try {

        // Conecte-se ao banco de dados 
        include("conexao.php");

        // Verifique se o e-mail existe no banco de dados
        $query = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email_do_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // O e-mail não foi encontrado no banco de dados, exiba uma mensagem de erro
            header("Location: index.html?ene");
            session_destroy();
        } else {
            // Gerando um código de recuperação único
            $codigo_recuperacao_dois_fatores = bin2hex(random_bytes(2));

            // Criptografando o código de recuperação antes de armazená-lo no banco de dados
            $codigo_recuperacao_dois_fatores_criptografado = password_hash($codigo_recuperacao_dois_fatores, PASSWORD_DEFAULT);

            // Inserindo o código criptografado no banco de dados junto com o e-mail do usuário
            $query = "UPDATE usuario SET codigo_verificacao_dois_fatores = ? WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $codigo_recuperacao_dois_fatores_criptografado, $email_do_usuario);
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
                    $mail->addAddress($email_do_usuario);     //Add a recipient


                    //Content
                    $mail->isHTML(true);
                    // Assunto e corpo do e-mail
                    $mail->Subject = 'Autenticacao de Dois Fatores';
                    $mail->Body = '
          <!DOCTYPE html>
          <html>
          <head>
          </head>
          <body>
            <h1 style="color: #007bff;">Autenticação de Dois Fatores</h1>
            <p>Olá!</p>
            <p>Use o código de ativação abaixo para logar usando a autenticação de dois fatores:</p>
            <div style="background-color: #f5f5f5; padding: 10px; border-radius: 5px; margin: 20px 0;">
              <h2 style="color: #007bff;">Código de Ativação:</h2>
              <p style="font-size: 24px;">' . $codigo_recuperacao_dois_fatores . '</p>
            </div>
            <p>Se você não tentou fazer login, você pode ignorar este e-mail com segurança.</p>
            <p>Atenciosamente,</p>
            <p>Sua equipe de suporte</p>
          </body>
          </html>';

                    $mail->send();

                    // Redirecione o usuário para a página de confirmação
                    $_SESSION["codigo-recuperacao"] = $codigo_recuperacao_dois_fatores;
                    header("Location: confirmar_codigo_dois_fatores_login.php");
                    session_write_close();
                    $stmt->close();
                    $conn->close();
                    exit();
                } catch (Exception $e) {
                    header("Location: index.html?ept");
                }
            } else {
                header("Location: index.html?ene");
                session_destroy();
            }
        }
    } catch (Exception $e) {
        header("Location: index.html?ept");
        session_destroy();
    }
}

function salva_log($log, $email)
{

    date_default_timezone_set('America/Sao_Paulo');
    $data_hora =  date('d/m/Y H:i:s', time());

    try {
        include("conexao.php");

        // Verifique se o usuário existe no banco de dados
        $query = "SELECT * FROM `usuario` WHERE `email` = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $id_user = 0;

        if ($result->num_rows > 0) {

            $dados_do_usuario = $result->fetch_assoc();
            $id_user = $dados_do_usuario['id'];
        }

        // Inserir os dados na tabela de usuários
        $stmt = $conn->prepare("INSERT INTO `logs`(`id_user`, `log`, `data_hora`) VALUES ('$id_user','$log','$data_hora')");
        // $stmt->bind_param("sss", $id_user, $log, $data_hora);
        $stmt->execute();
    } catch (Exception $e) {

        echo '' . $e->getMessage() . '';
    }
}

function cria_log($log, $email)
{

    /*########################################################################
    ## TABELA DE LOGS                                                       ##
    ##                                                                      ##
    ## LOG 0: SUCESSO logou corretamente;                                   ##
    ## LOG 1: SUCESSO cadastrou corretamente;                               ##
    ## LOG 2: SUCESSO recuperou senha corretamente;                         ##
    ## LOG 3: ERRO de senha incorreta;                                      ##
    ## LOG 4: ERRO de usuario incorreto;                                    ##
    ## LOG 5: ERRO ao recuperar senha, email inexistente no banco;          ##
    ## LOG 6: ERRO ao recuperar senha, codigo incorreto;                    ##
    ## LOG 7: ERRO ao tentar cadastrar usuario, email existente no banco;   ##
    ## LOG 8: ERRO ao tentar cadastrar usuario, falha ao inserir no banco;  ##
    ## LOG 9: SUCESSO ao trocar senha;                                      ##
    ## LOG 10: ERRO ao trocar senha;                                        ##
    ##                                                                      ##
    ########################################################################*/

    date_default_timezone_set('America/Sao_Paulo');

    if ($log == 'LOG0') {

        $data = "LOG 0: O USUÁRIO " . $email . " LOGOU COM SUCESSO AS " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 0: O USUARIO LOGOU COM SUCESSO';
    } elseif ($log == 'LOG1') {

        $data = "LOG 1: O USUÁRIO " . $email . " SE CADASTROU COM SUCESSO AS " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 1: O USUÁRIO SE CADASTROU COM SUCESSO';
    } elseif ($log == 'LOG2') {

        $data = "LOG 2: O USUÁRIO " . $email . " RECUPEROU A SENHA COM SUCESSO AS " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 2: O USUÁRIO RECUPEROU A SENHA COM SUCESSO';
    } elseif ($log == 'LOG3') {

        $data = "LOG 3: O USUÁRIO " . $email . " TENTOU LOGAR COM A SENHA INCORRETA AS " . date('d/m/Y H:i:s', time());
        $data_bd = "LOG 3: O USUÁRIO TENTOU LOGAR COM A SENHA INCORRETA";
    } elseif ($log == 'LOG4') {

        $data = "LOG 4: O USUÁRIO " . $email . " TENTOU LOGAR COM EMAIL INEXISTENTE " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 4: O USUÁRIO TENTOU LOGAR COM EMAIL INEXISTENTE';
    } elseif ($log == 'LOG5') {

        $data = "LOG 5: O USUÁRIO " . $email . " TENTOU RECUPERAR SENHA COM UM EMAIL EXISTENTE AS " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 5: O USUÁRIO TENTOU RECUPERAR SENHA COM UM EMAIL EXISTENTE';
    } elseif ($log == 'LOG6') {

        $data = "LOG 6: O USUÁRIO " . $email . " TENTOU RECUPERAR SENHA COM CODIGO INCORRETO AS " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 6: O USUÁRIO TENTOU RECUPERAR SENHA COM CODIGO INCORRETO';
    } elseif ($log == 'LOG7') {

        $data = "LOG 7: O USUÁRIO " . $email . " TENTOU CADASTRAR UM EMAIL JÁ EXISTENTE AS " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 7: O USUÁRIO TENTOU CADASTRAR UM EMAIL JÁ EXISTENTE';
    } elseif ($log == 'LOG8') {

        $data = "LOG 8: O USUÁRIO " . $email . " TENTOU CADASTRAR NO BANCO E HOUVE FALHA AS " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 8: O USUÁRIO TENTOU CADASTRAR NO BANCO E HOUVE FALHA';
    } elseif ($log == 'LOG9') {

        $data = "LOG 9: O USUÁRIO " . $email . " TROCOU A SENHA COM SUCESSO AS " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 9: O USUÁRIO TROCOU A SENHA COM SUCESSO';
    } elseif ($log == 'LOG10') {

        $data = "LOG 10: O USUÁRIO " . $email . " FALHOU AO TROCAR A SENHA AS " . date('d/m/Y H:i:s', time());
        $data_bd = 'LOG 10: O USUÁRIO TROCOU A SENHA COM SUCESSO';
    }

    $data = $data . PHP_EOL;
    $file = fopen("log.txt", "a"); // Abre o arquivo "arquivo.txt" para escrita (se não existir, ele será criado)        

    fwrite($file, $data);
    fclose($file); // Fecha o arquivo após a escrita

    if ($log != 'LOG8') {
        salva_log($data_bd, $email);
    }
}
