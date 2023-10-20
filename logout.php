<?php
session_start();

unset($_SESSION['email']);
unset($_SESSION['password']);
session_destroy();

header("Location: index.html?success= O usuário foi deslogado com sucesso!"); // Redirecionar para a página de login após sair