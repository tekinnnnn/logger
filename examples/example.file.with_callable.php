<?php

require_once '../Logger.php';

use \Tekinnnnn\Library\Logger\Logger;

$logger = new Logger(); // Default : dateFormat=DATE_FORMAT_RFC_2822
$logger->setLogType($logger::LOG_TYPE_FILE, '/home/tekin/Logger/', 'testLogger', $logger::LOG_PERIOD_DAILY);

$logger->log("Process Started",'^');

$log_finisher = function () use ($logger) { $logger->log('Process Finished.', '$', false, true); };

$logger->log('TEST');
$logger->log($logger->getLogPath(), 'INFO', $log_finisher);
$logger->log('TEST');
