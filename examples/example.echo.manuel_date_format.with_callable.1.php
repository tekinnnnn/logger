<?php

require_once '../Logger.php';

use \Tekinnnnn\Library\Logger\Logger;

$logger = new Logger("Y-m-d H:i:s");

$logger->log("Process Started",'^');

$log_finisher = function () use ($logger) { $logger->log('Process Finished.', '$', false, true); };

$logger->log('TEST');
$logger->log($logger->getLogPath(), 'INFO', $log_finisher);
$logger->log('TEST');
