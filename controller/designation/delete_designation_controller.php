<?php
namespace Designation;

class DeleteDesignationController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$db = $this->getDbConfig();
			$db->startTransaction();

			$ds = new \Designation($this->getDbConnection());

			$res = $ds->delete_designation($params);

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