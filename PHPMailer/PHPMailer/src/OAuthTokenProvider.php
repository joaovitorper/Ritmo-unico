<?php

namespace PHPMailer\PHPMailer;

/**
 * OAuthTokenProvider interface for authenticating with OAuth2.
 */
interface OAuthTokenProvider
{
    /**
     * Get the OAuth token as a base64-encoded string.
     *
     * @return string
     */
    public function getOauth64();
}
