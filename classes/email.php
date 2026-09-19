<?php

require_once __DIR__ . '/../config/enviar_email.php';

class Email
{
    public static function enviar(
        string $destinatarioEmail,
        string $assunto,
        string $corpoHtml,
        string $destinatarioNome = ''
    ): bool {
        if ($destinatarioNome === '') {
            $destinatarioNome = $destinatarioEmail;
        }

        return enviarEmail(
            $destinatarioEmail,
            $destinatarioNome,
            $assunto,
            $corpoHtml
        );
    }
}
