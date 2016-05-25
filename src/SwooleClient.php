<?php

namespace WsConsoleClient;

class SwooleClient extends ClientAbstract
{
    /**
     * @var \swoole_client
     */
    private $_client;

    public function connect() {
        if (false === $uri = parse_url($this->_socket)) {
            throw new \Exception("Invalid socket uri.");
        }

        $timeout = array_key_exists('timeout', $this->_options) ? $this->_options['timeout'] : 5;

        switch ($uri['scheme']) {
            case 'tcp':
                $this->_client = new \swoole_client(SWOOLE_TCP);
                if (! $this->_client->connect($uri['host'], $uri['port'], $timeout)) {
                    throw new \Exception("Connected failed.");
                }
                break;

            default:
                throw new \Exception("Unsupported socket scheme.");
        }
    }

    public function send($message, $channel = '', $time = 0) {
        $message = $this->buildMessage($message, $channel, $time);
        $this->packMessage($message);
        $this->_client->send($message);
    }
}