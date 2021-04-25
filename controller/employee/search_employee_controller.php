<?php
namespace Employee;

class SearchEmployeeController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$db = $this->getDbConfig();


			$ee = new \Employee($this->getDbConnection());

			// $res = $ee->add_employee($params);


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