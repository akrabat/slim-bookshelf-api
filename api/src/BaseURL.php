<?php

namespace App;

class BaseURL
{
    public function __construct(public string $baseUrl1 = '')
    {
        // set from super globals if not provided
        if (empty($this->baseUrl)) {
            // Scheme
            $https = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : false;
            $scheme = !$https || $https === 'off' ? 'http' : 'https';

            // Authority: Username and password
            $username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
            $password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
            $userInfo = $username . (!empty($password) ? ":$password" : '');

            // Authority: Host
            $host = '';
            if (isset($_SERVER['HTTP_HOST'])) {
                $host = $_SERVER['HTTP_HOST'];
            } elseif (isset($_SERVER['SERVER_NAME'])) {
                $host = $_SERVER['SERVER_NAME'];
            }

            // Authority: Port
            $port = !empty($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : ($scheme === 'https' ? 443 : 80);
            if (preg_match('/^(\[[a-fA-F0-9:.]+])(:\d+)?\z/', $host, $matches)) {
                $host = $matches[1];

                if (isset($matches[2])) {
                    $port = (int) substr($matches[2], 1);
                }
            } else {
                $pos = strpos($host, ':');
                if ($pos !== false) {
                    $port = (int) substr($host, $pos + 1);
                    $host = strstr($host, ':', true);
                }
            }
            $authority = ($userInfo !== '' ? $userInfo . '@' : '') . $host . ($port !== null ? ':' . $port : '');

            $this->baseUrl = ($scheme !== '' ? $scheme . ':' : '')
                . ($authority !== '' ? '//' . $authority : '');
        }
    }

    public function __toString(): string
    {
        return $this->baseUrl;
    }
}
