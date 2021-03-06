<?php
namespace Employee;

class SearchEmployeeController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$ee = new \Employee($this->getDbConnection());
			$res = $ee->search_employee($params);
			$this->response = [
				'status' => 'success',
				'res' => $res
			];

			$this->printResponse();
		} catch ( Exception $e ) {
			$this->error_log($e->getMessage());
		}
	}
}

?>