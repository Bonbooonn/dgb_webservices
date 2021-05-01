<?php
namespace Employee;

class GetEmployeeDetailsController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$ee = new \Employee($this->getDbConnection());
			$res = $ee->get_employee_details($params);
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