<?php
namespace Holiday;

class SearchHolidayController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$db = $this->getDbConfig();

			$hy = new \Holiday($this->getDbConnection());

			$res = $hy->search_holiday($params);

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