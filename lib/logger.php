<?php

class Logger {
	
	private $log_dir = DOC_ROOT . '/tmp/log/';
	public function __construct() {

		if( !file_exists($this->log_dir) && !is_dir($this->log_dir) ) {
			@mkdir($this->log_dir, 0755, true);
		}

	}

	public function debug_log($msg) {
		$date_now = date(DEFAULT_DATE_TIME_FORMAT);
		$message = "[APP LOGGER {$date_now}] : " . $msg;
		file_put_contents($this->log_dir . 'debug.log', $message . PHP_EOL, FILE_APPEND);
	}

	public function error_log($msg) {
		$date_now = date(DEFAULT_DATE_TIME_FORMAT);
		$message = "[APP ERROR LOGGER {$date_now}] : " . $msg;
		file_put_contents($this->log_dir . 'error.log', $message . PHP_EOL, FILE_APPEND);
	}
}

?>