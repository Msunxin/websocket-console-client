<?php

namespace WsConsoleClient;

class Client
{
    protected $instance;

    /**
     * Client constructor.
     * @param $socket
     * @param int $timeout
     * @throws \Exception
     */
    public function __construct($socket, $timeout = 5) {
        $options = array(
            'timeout' => $timeout,
        );

        if (false === $uri = parse_url($socket)) {
            throw new \Exception("Invalid socket uri.");
        }

        if ($uri['scheme'] === 'tcp' and extension_loaded('swoole')) {
            $this->instance = new SwooleClient($socket, $options);
        } else {
            $this->instance = new HoaClient($socket, $options);
        }
    }

    /**
     * @return ClientAbstract
     */
    public function getInstance() {
        return $this->instance;
    }
}