<?php

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
    ##                                                                      ##
    ########################################################################*/

    date_default_timezone_set('America/Sao_Paulo');

    if ($log == 'LOG0') {
       
        $data = "LOG 0: O USUÁRIO " . $email . " LOGOU COM SUCESSO AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG1') {

        $data = "LOG 1: O USUÁRIO " . $email . " SE CADASTROU COM SUCESSO AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG2') {

        $data = "LOG 2: O USUÁRIO " . $email . " RECUPEROU A SENHA COM SUCESSO AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG3') {

        $data = "LOG 3: O USUÁRIO " . $email . " TENTOU LOGAR COM A SENHA INCORRETA AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG4') {

        $data = "LOG 4: O USUÁRIO " . $email . " TENTOU LOGAR COM EMAIL INEXISTENTE " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG5') {

        $data = "LOG 5: O USUÁRIO " . $email . " TENTOU RECUPERAR SENHA COM UM EMAIL EXISTENTE AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG6') {

        $data = "LOG 6: O USUÁRIO " . $email . " TENTOU RECUPERAR SENHA COM CODIGO INCORRETO AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG7') {

        $data = "LOG 7: O USUÁRIO " . $email . " TENTOU CADASTRAR UM EMAIL JÁ EXISTENTE AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG8') {

        $data = "LOG 8: O USUÁRIO " . $email . " TENTOU CADASTRAR NO BANCO E HOUVE FALHA AS " . date('d/m/Y H:i:s', time());

    }

    $data = $data . PHP_EOL;
    $file = fopen("log.txt", "a"); // Abre o arquivo "arquivo.txt" para escrita (se não existir, ele será criado)        

    fwrite($file, $data);
    fclose($file); // Fecha o arquivo após a escrita


}
