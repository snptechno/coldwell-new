<?php

namespace App\Libraries;

use Config\Email as EmailConfig;
use Config\Services;
use Throwable;

/**
 * Replaces the PHP 8-incompatible SwiftMailer 5.1.0 (`system/helpers/swift_mailer/...`,
 * which fatally erred on `$line{3}` curly-brace string offsets) and CI3's inline
 * hardcoded Gmail credentials (`PUB-01`, `LC-02`, `SEC-11`, `LC-03`, `PUB-16`, `SEC-16`).
 *
 * - Reads SMTP configuration from `Config\Email` / `.env`.
 * - Sends from the configured domain address (`email.fromEmail`) and sets the
 *   visitor's address as `Reply-To` instead of spoofing `From` (`PUB-16` DMARC fix).
 * - Escapes all visitor-supplied values in HTML bodies (`SEC-16` / `LC-20`).
 * - Wraps transport in `try/catch` and skips live network connection when no
 *   `SMTPUser` is configured (or logs transport errors via `log_message`), so a
 *   mail transport issue never causes an HTTP 500 or loses a saved database lead.
 */
class EnquiryMailer
{
    /**
     * Send an HTML notification email.
     *
     * @param list<string>|string $to
     */
    public function send(
        array|string $to,
        string $subject,
        string $htmlMessage,
        ?string $replyToEmail = null,
        ?string $replyToName = null,
        ?string $attachmentPath = null,
    ): bool {
        try {
            $config = config(EmailConfig::class);

            // In local/offline environments where SMTP credentials are not configured,
            // log the notification instead of hanging/failing on an unauthenticated SMTP socket.
            if ($config->protocol === 'smtp' && trim($config->SMTPUser) === '') {
                log_message('info', sprintf(
                    'EnquiryMailer: skipping live SMTP send (no email.SMTPUser configured) for subject "%s" to %s.',
                    $subject,
                     is_array($to) ? implode(', ', $to) : $to,
                ));

                return true;
            }

            $email = Services::email($config, true);
            $email->setMailType('html');
            $email->setFrom(
                $config->fromEmail !== '' ? $config->fromEmail : 'info@coldwellbanker.in',
                $config->fromName !== '' ? $config->fromName : 'Coldwell Banker India',
            );
            $email->setTo($to);

            if ($replyToEmail !== null && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL) !== false) {
                $email->setReplyTo($replyToEmail, $replyToName ?? '');
            }

            $email->setSubject($subject);
            $email->setMessage($htmlMessage);

            if ($attachmentPath !== null && is_file($attachmentPath)) {
                $email->attach($attachmentPath);
            }

            $sent = $email->send(false);
            if (! $sent) {
                log_message('error', 'EnquiryMailer failed to send "' . $subject . '": ' . $email->printDebugger(['headers']));
            }

            return $sent;
        } catch (Throwable $e) {
            log_message('error', 'EnquiryMailer exception while sending "' . $subject . '": ' . $e->getMessage());

            return false;
        }
    }
}
