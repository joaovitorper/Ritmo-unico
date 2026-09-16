<?php

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORTA', 587);
define('MAIL_USUARIO', 'seuemail@gmail.com');      // <-- troque pelo seu Gmail
define('MAIL_SENHA_APP', 'xxxx xxxx xxxx xxxx');    // <-- troque pela senha de app
define('MAIL_NOME_REMETENTE', 'Ritmo Único');
define('URL_BASE', getenv('URL_BASE') ?: 'http://localhost/Ritmo-unico');

// Para o Gmail, use a senha de app do Gmail (não a senha da conta principal).
// Se quiser, pode definir as variáveis de ambiente:
// MAIL_USUARIO, MAIL_SENHA_APP e URL_BASE