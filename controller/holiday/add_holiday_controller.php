<?php
namespace Holiday;

class AddHolidayController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$db = $this->getDbConfig();
			$db->startTransaction();

			$hy = new \Holiday($this->getDbConnection());

			$res = $hy->add_holiday($params);

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