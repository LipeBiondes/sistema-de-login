<?php
include("verificar_sessao.php");
include("funcoes.php");
$email_do_usuario = $_SESSION["email"];
enviar_email_dois_fatores($email_do_usuario);
