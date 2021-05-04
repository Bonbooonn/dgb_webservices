<?php
namespace Attendance;

class CheckAttendanceController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$db = $this->getDbConfig();
			$db->startTransaction();

			$att = new \Attendance($this->getDbConnection());

			$res = $att->check_attendance($params);

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