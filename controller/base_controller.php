<?php

class BaseController {
	private $app = [];
	private $dbConnection = null;
	private $logger = null;
	private $mainApp = null;
	public $response = [];

	
	public function __construct($app) {
		$this->app = $app['router']; // router
		$this->dbConnection = $app['db'];
		$this->logger = $app['logger'];
		$this->mainApp = $app;
	}

	public function getApp() {
		return $this->mainApp;
	}

	public function getRequestParams() {
		return $this->app->getRequestBody();
	}

	public function printResponse() {
		echo json_encode($this->response);
	}

	public function getDbConnection() {
		return $this->dbConnection->getDb();
	}

	public function getDbConfig() {
		return $this->dbConnection;
	}

	public function debug_log($msg) {
		$this->logger->debug_log($msg);
	}

	public function error_log($msg) {
		$this->logger->error_log($msg);
	}


}

?>