<?php
namespace Attendance;

class GetAttendanceController extends \BaseController {

	public function __construct($app) {
		parent::__construct($app);
	}

	public function exec() {
		try {
			$params = $this->getRequestParams();
			$db = $this->getDbConfig();

			$att = new \Attendance($this->getDbConnection());

			$res = $att->get_attendance($params);

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