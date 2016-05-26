<?php

use WsConsoleClient\Cli;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\OptionPrinter\ConsoleOptionPrinter;

$specs = new OptionCollection;
$specs->add('c|channel?', 'Target channel.' )->isa('String');
$specs->add('e|error?', 'Monitor PHP error log file.' );
$specs->add('f|file?', 'Log file to monitor.' )->isa('File');
$specs->add('h|host?', 'WebSocket server host.')->isa('String')->defaultValue('127.0.0.1');
$specs->add('m|message?', 'Publish message.' )->isa('String');
$specs->add('p|port:', 'WebSocket server port.')->isa('Number')->defaultValue(9028);
$specs->add('help', 'Print this help.');

try {
    $option = (new OptionParser($specs))->parse($argv);
} catch (\Exception $ex) {
    Cli::error($ex->getMessage(), 1, false);
}

if ($option->help) {
    Cli::out(
        "Usage: ./ws-client [options] -m \"some message\"" . PHP_EOL .
        "   Or: ./ws-client [options] -f /path/to/access.log" . PHP_EOL . PHP_EOL .
        "Options:" . PHP_EOL .
        (new ConsoleOptionPrinter)->render($specs)
    );
} else {
    $uri = sprintf("ws://%s:%d", $option->host, $option->port);
    Cli::out(sprintf("Connecting to %s ...", $uri));
    $client = (new WsConsoleClient\Client($uri))->getInstance();
    $client->connect();
    if ($option->error) {
        if (is_file(ini_get("error_log"))) {
            (new WsConsoleClient\FileClient($client))->monitor(ini_get("error_log"), "PHP error log");
        } else {
            Cli::out("Failed to detect PHP log file. Try -f option instead.");
        }
    } elseif ($option->file) {
        (new WsConsoleClient\FileClient($client))->monitor($option->file, $option->channel);
    } elseif (! empty($option->message)) {
        $client->send($option->message, $option->channel);
    } else {
        Cli::out("Both -m and -f options are invalid.");
    }
}