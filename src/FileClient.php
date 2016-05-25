<?php

namespace WsConsoleClient;

class FileClient
{
    protected $lastPosition;
    protected $client;

    public function __construct(ClientAbstract $client) {
        $this->client = $client;
    }

    public function monitor($file, $channel) {
        if (false === $fp = @fopen($file, "r")) {
            Cli::error("Failed to open $file");
        } else {
            Cli::out("Monitoring file: $file");
        }

        while (1) {
            if (-1 === fseek($fp, 0, SEEK_END) or !$pos = ftell($fp)) {
                goto retry;
            }

            if ($this->lastPosition === null or $this->lastPosition > $pos) {
                $this->lastPosition = $pos;
                goto retry;
            }

            if ($this->lastPosition < $pos) {
                fseek($fp, $this->lastPosition - $pos, SEEK_CUR);
                if (false === $content = fread($fp, $pos - $this->lastPosition)) {
                    goto retry;
                }
                try {
                    $this->client->send($content, $channel);
                } catch (\Exception $ex) {
                    Cli::error($ex->getMessage());
                }
                $this->lastPosition = $pos;
            }
            retry:
            usleep(200000);
        }
    }
}