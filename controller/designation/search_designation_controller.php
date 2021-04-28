<?php
namespace Designation;

class SearchDesignationController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$db = $this->getDbConfig();

			$ds = new \Designation($this->getDbConnection());

			$res = $ds->search_designation($params);

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