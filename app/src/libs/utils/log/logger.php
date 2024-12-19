<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../../vendor/autoload.php';

function getLogger(string $component): Logger
{
    static $loggers = []; // Memorized logger instances, keyed by component name

    // Return the existing logger if it is already created
    if (isset($loggers[$component])) {
        return $loggers[$component];
    }

    // Define the log directory and file path
    $logDirectory = __DIR__ . "/logs";  // Path to the log directory
    $logFile = $logDirectory . "/{$component}.log";  // Path to the log file

    // Check if the directory exists, if not, create it
    if (!is_dir($logDirectory)) {
        mkdir($logDirectory, 0777, true);  // Create the directory recursively with full permissions
    }

    // Create a new logger for the given component
    $logger = new Logger($component);
    
    // Add a handler to write logs to the file
    $logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

    // Memorize the created logger for future use
    $loggers[$component] = $logger;

    return $logger;
}
