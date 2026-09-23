<?php

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORTA', 587);

define('MAIL_USUARIO', 'jovipepa.10@gmail.com');

/*
 * Cole aqui a NOVA senha de app gerada pelo Google.
 * Coloque os 16 caracteres sem espaços.
 */
define('MAIL_SENHA_APP', 'owtdntwqrgcjbwqi');

define('MAIL_NOME_REMETENTE', 'Ritmo Único');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';

define(
    'URL_BASE',
    getenv('URL_BASE') ?: rtrim($scheme . '://' . $host, '/')
);