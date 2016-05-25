<?php

namespace WsConsoleClient;

use Hoa\Websocket\Client as WsClient;
use Hoa\Socket\Client as SocketClient;

class HoaClient extends ClientAbstract
{
    private $_isTcp;
    private $_client;

    public function connect() {
        if (false === $uri = parse_url($this->_socket)) {
            throw new \Exception("Invalid socket uri.");
        }

        $timeout = array_key_exists('timeout', $this->_options) ? $this->_options['timeout'] : 5;

        switch ($uri['scheme']) {
            case 'tcp':
                $this->_isTcp = true;
                $this->_client = new SocketClient($this->_socket, $timeout);
                break;

            case 'ws':
                $this->_client = new WsClient(
                    new SocketClient($this->_socket, $timeout)
                );
                $this->_client->setHost("php.html.js.cn");
                break;

            default:
                throw new \Exception("Unsupported socket scheme.");
        }

        $this->_client->connect();
    }

    public function send($message, $channel = '', $time = 0) {
        $message = $this->buildMessage($message, $channel, $time);
        try {
            if ($this->_isTcp) {
                $newLength = $this->packMessage($message);
                $this->_client->write($message, $newLength);
            } else {
                $this->_client->send($message);
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

}