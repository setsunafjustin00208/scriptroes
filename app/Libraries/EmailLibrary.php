<?php

namespace App\Libraries;

class EmailLibrary
{
    // Add your library methods here

    /**
     * Send an email using CodeIgniter's email service and SMTP credentials from .env
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $message Email body (HTML allowed)
     * @param array $options Optional: ['from' => ..., 'fromName' => ..., 'cc' => ..., 'bcc' => ..., 'attachments' => [...]]
     * @return bool|string true on success, error message on failure
     */
    public function send(string $to, string $subject, string $message, array $options = []): bool|string
    {
        $email = \Config\Services::email();
        // Set SMTP config from .env
        $config = [
            'protocol'  => 'smtp',
            'SMTPHost'  => getenv('email.default.host'),
            'SMTPUser'  => getenv('email.default.username'),
            'SMTPPass'  => getenv('email.default.password'),
            'SMTPPort'  => (int) getenv('email.default.port'),
            'SMTPCrypto'=> getenv('email.default.encryption') ?: 'tls',
            'mailType'  => 'html',
            'charset'   => 'UTF-8',
            'newline'   => "\r\n",
            'CRLF'      => "\r\n",
        ];
        $email->initialize($config);
        $from =  getenv('email.default.username');
        $fromName = getenv('email.default.fromName');
        // Validate sender email
        if (!$from || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid or missing sender email address.';
        }
        $email->setTo($to);
        $email->setFrom($from, $fromName);
        $email->setSubject($subject);
        $email->setMessage($message);
        if (!empty($options['cc'])) {
            $email->setCC($options['cc']);
        }
        if (!empty($options['bcc'])) {
            $email->setBCC($options['bcc']);
        }
        if (!empty($options['attachments']) && is_array($options['attachments'])) {
            foreach ($options['attachments'] as $file) {
                $email->attach($file);
            }
        }
        if ($email->send()) {
            return true;
        } else {
            return $email->printDebugger(['headers', 'subject', 'body']);
        }
    }

    /**
     * Send a confirmation code to a registered email address.
     *
     * @param string $to Recipient email address
     * @param string $code Confirmation code
     * @param array $options Optional: ['from' => ..., 'fromName' => ...]
     * @return bool|string true on success, error message on failure
     */
    public function sendConfirmationCode(string $to, string $code, array $options = []): bool|string
    {
        $subject = 'Your Account Confirmation Code';
        $message = '<h2>Account Confirmation</h2>' .
                   '<p>Thank you for registering. Please use the following confirmation code to unlock your account:</p>' .
                   '<div style="font-size:1.5em;font-weight:bold;margin:1em 0;">' . htmlspecialchars($code) . '</div>' .
                   '<p>If you did not request this, please ignore this email.</p>';
        return $this->send($to, $subject, $message, $options);
    }
}
