<?php
// simple-smtp.php - Minimal SMTP sender for GoDaddy shared hosting
class SimpleSMTPMailer {
    private $host = 'localhost'; // GoDaddy's SMTP relay server
    private $port = 25; // Default port for GoDaddy shared hosting (no auth required)
    private $from = 'it@wisdomj.in';
    private $fromName = 'Journal Admin Portal';

    public function send($to, $toName, $subject, $body, $replyTo = null) {
        // Open connection to SMTP server
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 30);
        if (!$socket) {
            error_log("SMTP Connect failed: $errstr ($errno)");
            return false;
        }

        // Read greeting
        fgets($socket, 512);

        // HELO
        fputs($socket, "HELO " . $_SERVER['SERVER_NAME'] . "\r\n");
        fgets($socket, 512);

        // MAIL FROM
        fputs($socket, "MAIL FROM: <" . $this->from . ">\r\n");
        fgets($socket, 512);

        // RCPT TO
        fputs($socket, "RCPT TO: <" . $to . ">\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '250') {
            error_log("SMTP RCPT failed: $response");
            fclose($socket);
            return false;
        }

        // DATA
        fputs($socket, "DATA\r\n");
        fgets($socket, 512);

        // Headers
        $headers = "From: " . $this->fromName . " <" . $this->from . ">\r\n";
        $headers .= "To: " . $toName . " <" . $to . ">\r\n";
        $headers .= "Subject: $subject\r\n";
        if ($replyTo) $headers .= "Reply-To: $replyTo\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "\r\n";

        // Send headers + body
        fputs($socket, $headers . $body . "\r\n.\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '250') {
            error_log("SMTP DATA failed: $response");
            fclose($socket);
            return false;
        }

        // QUIT
        fputs($socket, "QUIT\r\n");
        fclose($socket);

        error_log("SMTP Email sent to: $to");
        return true;
    }
}
?>