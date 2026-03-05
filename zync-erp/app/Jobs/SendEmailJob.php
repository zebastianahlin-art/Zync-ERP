<?php

declare(strict_types=1);

namespace App\Jobs;

/**
 * SendEmailJob — Skickar e-post asynkront via jobbkön.
 *
 * Förväntad payload:
 *   to      string  Mottagarens e-postadress
 *   subject string  Ämnesrad
 *   body    string  Meddelandetext (HTML eller text)
 *   from    string  (valfri) Avsändaradress
 */
class SendEmailJob
{
    public function handle(array $payload): void
    {
        $to      = filter_var($payload['to'] ?? '', FILTER_VALIDATE_EMAIL);
        $subject = trim((string) ($payload['subject'] ?? ''));
        $body    = (string) ($payload['body'] ?? '');
        $from    = filter_var($payload['from'] ?? '', FILTER_VALIDATE_EMAIL)
                   ?: 'noreply@zync-erp.se';

        if ($to === false || $subject === '' || $body === '') {
            throw new \InvalidArgumentException('SendEmailJob: ogiltiga payload-värden (to, subject, body krävs).');
        }

        $headers  = "From: {$from}\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "MIME-Version: 1.0\r\n";

        $result = mail($to, $subject, $body, $headers);

        if (!$result) {
            throw new \RuntimeException("SendEmailJob: mail() misslyckades för {$to}.");
        }
    }
}
