<?php

function cria_log($log, $email)
{
    date_default_timezone_set('America/Sao_Paulo');

    if ($log == 'LOG0') {
       
        $data = "LOG 0: O USUÁRIO " . $email . " LOGOU COM SUCESSO AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG1') {

    } elseif ($log == 'LOG2') {

    } elseif ($log == 'LOG3') {

    } elseif ($log == 'LOG4') {

        $data = "LOG 4: O USUÁRIO " . $email . " TENTOU LOGAR AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG5') {

        $data = "LOG 5: O USUÁRIO " . $email . " TENTOU LOGAR AS " . date('d/m/Y H:i:s', time());

    } elseif ($log == 'LOG6') {
    } elseif ($log == 'LOG7') {
    } elseif ($log == 'LOG8') {
    } elseif ($log == 'LOG9') {
    }

    $data = $data . PHP_EOL;
    $file = fopen("log.txt", "a"); // Abre o arquivo "arquivo.txt" para escrita (se não existir, ele será criado)        

    fwrite($file, $data);
    fclose($file); // Fecha o arquivo após a escrita


}
