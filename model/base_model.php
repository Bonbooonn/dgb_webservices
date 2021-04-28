<?php

class BaseModel {
	private $db = null;
	private $table = null;
	private $ormObj = null;
	public $appOrm = null;

	public function __construct($db, $table = null) {
		$this->db = $db;
		$this->table = $table;

		if ( empty($this->ormObj) ) {
			$this->setOrm();
		}

	}

	private function setOrm() {
		$this->ormObj = new Orm($this->db, $this->table);
		$this->appOrm = $this->ormObj;
	}

	public function getOrm() {
		if ( empty($this->ormObj) ) {
			$this->setOrm();
			return $this->ormObj;
		} else {
			$this->ormObj->initConn();
			return $this->ormObj;
		}
	}

	public function debug_log($msg) {
		$logger = $GLOBALS['app']['logger'];
		$logger->debug_log($msg);
	}

	public function error_log($msg) {
		$logger = $GLOBALS['app']['logger'];
		$logger->error_log($msg);
	}

	public function startsWith($str, $char){
	    return $str[0] === $char;
	}

}

?>