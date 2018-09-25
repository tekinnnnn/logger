<?php

namespace Tekinnnnn\Library\Logger;

class Logger
{
	const LOG_TYPE_ECHO = 1;
	const LOG_TYPE_FILE = 2;
	
	const LOG_FILE_EXTENSION = '.log';
	
	const LOG_PERIOD_DAILY   = 'Ymd';
	const LOG_PERIOD_WEEKLY  = 'YW';
	const LOG_PERIOD_MONTHLY = 'Ym';
	const LOG_PERIOD_YEARLY  = 'Y';
	
	const DATE_FORMAT_ISO_8601 = 'c';
	const DATE_FORMAT_RFC_2822 = 'r';
	const DATE_FORMAT_CALLABLE = false;
	
	const LINE_FINISHER = "\r\n";
	
	private $logType     = self::LOG_TYPE_ECHO;
	private $logPeriod   = self::LOG_PERIOD_DAILY;
	private $logPath     = null;
	private $logBaseName = 'log';
	
	private        $dateFormat = self::DATE_FORMAT_RFC_2822;
	private static $dateCallback;
	
	function __construct($dateFormat = self::DATE_FORMAT_RFC_2822)
	{
		$this->setDateFormat($dateFormat);
	}
	
	function setLogType($logType, $logPath = null, $logBaseName = null, $logPeriod = null)
	{
		$this->logType = $logType;
		
		if ($logType == self::LOG_TYPE_ECHO) return $this;
		
		if (!$logPath || !$logBaseName || !$logPeriod) return $this->setLogType(self::LOG_TYPE_ECHO);
		
		$this->setLogFile($logPath, $logBaseName, $logPeriod);
		
		return $this;
	}
	
	function setLogFile($logPath = null, $logBaseName = null, $logPeriod = null)
	{
		$this->setLogPath($logPath);
		$this->setLogBaseName($logBaseName);
		$this->setLogPeriod($logPeriod);
		
		return $this;
	}
	
	private function setLogPath($logPath)
	{
		$this->logPath = $logPath;
		
		return $this;
	}
	
	private function setLogBaseName($logBaseName)
	{
		$this->logBaseName = $logBaseName;
		
		return $this;
	}
	
	private function setLogPeriod($logPeriod)
	{
		$this->logPeriod = $logPeriod;
		
		return $this;
	}
	
	function getLogPath()
	{
		return $this->logPath . $this->logBaseName . date(".$this->logPeriod") . self::LOG_FILE_EXTENSION;
	}
	
	function setDateFormat($dateFormat)
	{
		if (!is_callable($dateFormat)) $this->dateFormat = $dateFormat;
		
		$this->setDateCallback($this->createDateCallbackFromFormat($dateFormat));
		
		return $this;
	}
	
	private function setDateCallback(callable $dateCallback)
	{
		$this->dateFormat = self::DATE_FORMAT_CALLABLE;
		static::$dateCallback = $dateCallback;
		
		return $this;
	}
	
	private function createDateCallbackFromFormat($dateFormat)
	{
		return function () use ($dateFormat) { return date($dateFormat); };
	}
	
	private static function getDate()
	{
		return call_user_func(self::$dateCallback);
	}
	
	public function log($message, $type = 'INFO', $callBack = false, $exit = false)
	{
		$log = $this->compileLog($message, $type);
		switch ($this->logType) {
			case self::LOG_TYPE_ECHO:
				$this->echoLog($log);
				break;
			case self::LOG_TYPE_FILE:
				$this->writeLog($log);
				break;
		}
		
		if (is_callable($callBack)) call_user_func($callBack);
		if ($exit) exit;
	}
	
	private function compileLog($message, $type)
	{
		return "[ " . static::getDate() . " ] [ $type ] $message" . self::LINE_FINISHER;
	}
	
	private function echoLog($message)
	{
		echo $message;
	}
	
	private function writeLog($message)
	{
		return file_put_contents($this->getLogPath(), $message, FILE_APPEND);
	}
}