<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../../vendor/autoload.php';

/**
 * Creates and returns a Logger instance for the specified component.
 * 
 * The logger is created only once for each component type (e.g., "home").
 * Logs are written to a specific file in the "logs" folder, with a separate file for each component.
 * If the "logs" folder does not exist, it is created automatically.
 * 
 * @param string $component The name of the component for which to create the logger (e.g., "home").
 * @return Logger The logger instance for the specified component.
 */
function getLogger(string $component = "generic"): Logger
{
    static $loggers = [];

    if (isset($loggers[$component])) {
        return $loggers[$component];
    }

    $logDirectory = __DIR__ . "/../../../logs";
    $logFile = $logDirectory . "/$component.log";

    if (!is_dir($logDirectory)) {
        mkdir($logDirectory, 0755, true);
    }

    $logger = new Logger($component);
    $logger->pushHandler(new StreamHandler($logFile, Level::Debug));

    $loggers[$component] = $logger;

    return $logger;
}
