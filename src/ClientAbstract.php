<?php

namespace WsConsoleClient;

abstract class ClientAbstract
{
    protected $_socket;
    protected $_options;

    /**
     * ClientAbstract constructor.
     *
     * @param string $socket
     * @param array $options
     */
    public function __construct($socket, $options = array()){
        $this->_socket = $socket;
        $this->_options = $options;
    }

    /**
     * @throws \Exception
     */
    abstract public function connect();

    /**
     * @param mixed $message
     * @param string $channel
     * @param int $time
     * @return string
     */
    abstract public function send($message, $channel = '', $time = 0);

    /**
     * TCP message
     *
     * @param $message
     * @return int
     */
    protected function packMessage(& $message) {
        $length = strlen($message);
        $message = pack('N', $length) . $message;
        return $length + 4;
    }

    /**
     * @param mixed $message
     * @param string $channel
     * @param int $time
     * @return string
     */
    protected function buildMessage($message, $channel = '', $time = 0) {
        return json_encode(array(
            'content' => $message,
            'channel' => $channel,
            'time' => $time ?: time(),
            'cmd' => 'publish',
        ));
    }
}