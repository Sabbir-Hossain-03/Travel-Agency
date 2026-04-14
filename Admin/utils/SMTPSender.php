<?php
namespace Admin\Utils;

require_once __DIR__ . '/SMTPConfig.php';

class SMTPSender {

    private static function get_response($socket) {
        $response = "";
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") break;
        }
        return $response;
    }

    private static function check_response($response, $expectedCode) {
        return substr(trim($response), 0, 3) == (string)$expectedCode;
    }

    public static function send($to, $subject, $body) {
        if (!defined('SMTP_ENABLED') || !SMTP_ENABLED) {
            return false;
        }

        $host     = SMTP_HOST;
        $port     = SMTP_PORT;
        $user     = SMTP_USER;
        $pass     = SMTP_PASS;
        $from     = SMTP_FROM;
        $fromName = SMTP_FROM_NAME;
        $useSSL   = defined('SMTP_SSL') && SMTP_SSL;
        $ehloHost = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

        // Use stream_context for all connections to allow relaxed SSL (important for shared hosts)
        $context = stream_context_create([
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ]);

        $remote = ($useSSL ? "ssl://" : "") . $host . ":" . $port;
        $socket = @stream_socket_client(
            $remote, $errno, $errstr, 15,
            STREAM_CLIENT_CONNECT, $context
        );

        if (!$socket) {
            error_log("SMTP Connection Failed: $errstr ($errno) on $remote");
            return false;
        }

        $greeting = self::get_response($socket);
        if (!self::check_response($greeting, 220)) {
            fclose($socket);
            return false;
        }

        // Send EHLO
        fputs($socket, "EHLO $ehloHost\r\n");
        $ehloResp = self::get_response($socket);

        // STARTTLS upgrade if 587
        if (!$useSSL) {
            fputs($socket, "STARTTLS\r\n");
            $tls = self::get_response($socket);
            if (self::check_response($tls, 220)) {
                // Upgrade to TLS with relaxed settings
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    error_log("SMTP TLS handshake failed");
                    fclose($socket);
                    return false;
                }
                // Re-send EHLO after TLS
                fputs($socket, "EHLO $ehloHost\r\n");
                self::get_response($socket);
            }
        }

        // AUTH LOGIN
        fputs($socket, "AUTH LOGIN\r\n");
        self::get_response($socket);

        fputs($socket, base64_encode($user) . "\r\n");
        self::get_response($socket);

        fputs($socket, base64_encode($pass) . "\r\n");
        $authResponse = self::get_response($socket);

        if (!self::check_response($authResponse, 235)) {
            error_log("SMTP Auth Failed: $authResponse");
            fclose($socket);
            return false;
        }

        // MAIL FROM / RCPT TO / DATA
        fputs($socket, "MAIL FROM: <$from>\r\n");
        self::get_response($socket);

        fputs($socket, "RCPT TO: <$to>\r\n");
        self::get_response($socket);

        fputs($socket, "DATA\r\n");
        self::get_response($socket);

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "To: <$to>\r\n";
        $headers .= "From: \"$fromName\" <$from>\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "Date: " . date("r") . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        fputs($socket, $headers . "\r\n" . $body . "\r\n.\r\n");
        $result = self::get_response($socket);

        fputs($socket, "QUIT\r\n");
        fclose($socket);

        return self::check_response($result, 250);
    }
}
