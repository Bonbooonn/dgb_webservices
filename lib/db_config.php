<?php

class DBConfig {

	private static $dbConnection = null;

	private $options = [
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4,sql_mode="TRADITIONAL"'
	];

	public function __construct() {

		$this->setDb();

	}

	private function setDb() {
		self::$dbConnection = new PDO('mysql:host='.DB_HOST.';dbname='.DATABASE, DB_USERNAME, DB_PASSWORD, $this->options);
		self::$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function getAttributes() {
		$attributes = array(
		    "AUTOCOMMIT", "ERRMODE", "CASE", "CLIENT_VERSION", "CONNECTION_STATUS",
		    "ORACLE_NULLS", "PERSISTENT", "SERVER_INFO", "SERVER_VERSION",
		);

		foreach ($attributes as $val) {
		    echo "PDO::ATTR_$val: ";
		    echo self::$dbConnection->getAttribute(constant("PDO::ATTR_$val")) . "\n" . "<br>";
		}
	}

	public function startTransaction() {
		self::$dbConnection->beginTransaction();
	}

	public function commitTransaction() {
		self::$dbConnection->commit();
	}

	public function rollbackTransaction() {
		self::$dbConnection->rollBack();
	}

	public function getDb() {

		if ( empty(self::$dbConnection) ) {
			$this->setDb();
		}

		return self::$dbConnection;
	}

	public function prepareQuery($query) {
		self::$dbConnection->prepare($query);
		return self::$dbConnection;
	}

	function __destruct() {
		self::$dbConnection = null;
	}
}

?>