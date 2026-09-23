<?php

echo '<h1>TESTE NOVO</h1>';
echo '<p>Arquivo correto do projeto Ritmo Único.</p>';

require_once __DIR__ . '/config/email.php';

echo '<p>Usuário: ' . htmlspecialchars(MAIL_USUARIO) . '</p>';
echo '<p>Tamanho da senha: ' . strlen(MAIL_SENHA_APP) . '</p>';

exit;