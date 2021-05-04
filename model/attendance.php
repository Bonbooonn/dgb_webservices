<?php

class Attendance extends \BaseModel {

	public function __construct($db) {
		parent::__construct($db, 'attendance');
	}

	public function check_attendance($params) {

		$dt = new DateTime();
		$date = $dt->format(DEFAULT_DATE_TIME_FORMAT);

		$insert = [];
		$res = [];
		if ( !empty($params['attendance_date']) ) {
			$insert['att_date'] = $params['attendance_date'];
		}

		if ( !empty($params['ee_id']) ) {
			$insert['ee_id'] = $params['ee_id'];
		}

		if ( !empty($params['holiday_flag']) ) {
			$insert['holiday_flag'] = $params['holiday_flag'];
		}

		if ( !empty($params['overtime']) ) {
			$insert['overtime'] = $params['overtime'];
		}

		if ( !empty($params['att_id']) ) {
			$insert['modified'] = $date;
			$att = $this->appOrm->update($insert, ['id' => $params['att_id']]);
			if ( !empty($params['overtime']) ) {
				$message = "Overtime Added!";
			} else {
				$message = "Attendance Updated!";
			}
		} else {
			$insert['created'] = $date;
			$att = $this->appOrm->insert($insert);
			$message = "Attendance checked!";
		}

		if ( empty($att) ) {
			return $this->return_message(false, "Failed to check attendance");
		}

		return [
			'msg' => $this->return_message(true, $message),
			'att_id' => !empty($att['id']) ? $att['id'] : ''
		];
	}

	public function get_attendance($params) {
		$ee = new \Employee($this->getOrm()->dbConn());
		$ee_res = $ee->search_employee($params);
		$ee_ids = array_column($ee_res['list'], 'ee_id');

		$att = $this->appOrm
			->select('attendance.id', 'att_id')
			->select('attendance.ee_id')
			->select('attendance.att_date')
			->select('attendance.overtime')
			->where_raw('attendance.ee_id IN (' . join(',', $ee_ids) . ')')
			->where_raw("attendance.att_date >= '{$params['first_day']}' AND attendance.att_date <= '{$params['last_day']}'")
			->get_result_set();
		$this->debug_log($this->appOrm->get_latest_query());

		$att_res = !empty($att['result']) ? $att['result'] : [];

		foreach ( $ee_res['list'] as $key => &$val ) {

			if ( !empty($att_res) ) {
				foreach ( $att_res as $att_key => $att_val ) {

					if ( $att_val['ee_id'] == $val['ee_id'] ) {
						$val['att_data'][] = [
							'att_date' => $att_val['att_date'],
							'ot' => $att_val['overtime'],
							'att_id' => $att_val['att_id']
						];
					}

				}
			}

		}
		unset($val);

		return $ee_res;
	}

}

?>