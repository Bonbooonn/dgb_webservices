<?php
namespace Employee;

class AddEmployeeController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$db = $this->getDbConfig();
			$db->startTransaction();

			$ee = new \Employee($this->getDbConnection());

			$res = $ee->add_employee($params);

			$db->commitTransaction();
			$this->response = [
				'status' => 'success',
				'res' => $res
			];

			$this->printResponse();
		} catch ( Exception $e ) {
			$db->rollbackTransaction();
			$this->error_log($e->getMessage());
		}
	}
}

?>