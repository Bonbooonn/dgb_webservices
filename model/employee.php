<?php

class Employee extends \BaseModel {
	public function __construct($db) {
		parent::__construct($db, 'employee');
	}

	public function add_employee($params) {
		$this->error_log(json_encode($params));
		$date = date(DEFAULT_DATE_TIME_FORMAT);
		$insert = [];
		$res = [];
		$message = "";

		if ( empty($params['first_name']) || empty($params['last_name']) ) {
			return [];
		}

		$first_name = ucfirst(strtolower($params['first_name']));
		$last_name = ucfirst(strtolower($params['last_name']));

		if ( !empty($params['first_name']) ) {
			$insert['first_name'] = $first_name;
		}

		if ( !empty($params['last_name']) ) {
			$insert['last_name'] = $last_name;
		}

		$check_ee = $this->getOrm()
			->select('id')
			->where('first_name', $first_name)
			->where('last_name', $last_name)
			->get_one_result();

		if ( !empty($check_ee) ) {
			return [
				'success' => false,
				'message' => "Employee already exists!"
			];
		}

		if ( !empty($params['status']) ) {
			$insert['status'] = $params['status'];
		}

		if ( !empty($params['emp_code']) ) {
			$insert['emp_code'] = $params['emp_code'];
		}

		if ( !empty($params['designation_id']) ) {
			$insert['designation_id'] = $params['designation_id'];
		}

		$this->getOrm()->initConn();

		if ( !empty($params['email']) ) {
			
			$check_email = $this->getOrm()
				->select('id')
				->where('email', $params['email'])
				->get_one_result();

			if ( !empty($check_email) ) {
				return [
					'success' => false,
					'message' => "Email already exists!"
				];
			}

			$insert['email'] = $params['email'];

		}

		$this->getOrm()->initConn();

		if ( !empty($params['phone']) ) {
			$phone_no = "";
			if ( $this->startsWith($params['phone'], '0') ) {
				$phone_no = "+63" . substr($params['phone'], 1);
			} else if ( !$this->startsWith($params['phone'], '0') && !preg_match("/[+]63/", $params['phone']) ) {
				$phone_no = "+63" . $params['phone'];
			} else {
				$phone_no = $params['phone'];
			}

			$check_phone = $this->getOrm()
				->select('id')
				->where('phone', $phone_no)
				->get_one_result();

			if ( !empty($check_phone) ) {
				return [
					'success' => false,
					'message' => "Phone Number already exists!"
				];
			}

			$insert['phone'] = $phone_no;
		}

		if ( !empty($params['daily_salary']) ) {
			$insert['daily_salary'] = $params['daily_salary'];
		}

		if ( !empty($params['monthly_salary']) ) {
			$insert['monthly_salary'] = $params['monthly_salary'];
		}

		$this->getOrm()->initConn();
		if ( !empty($params['employee_id']) ) {
			$insert['modified'] = $date;
			$ee = $this->getOrm()->update($insert, ['id' => $params['employee_id']]);
			$message = "Employee Information Updated!";
		} else {
			$insert['created'] = $date;
			$ee = $this->getOrm()->insert($insert);
			$message = "Employee Information Added!";
		}

		if ( !empty($ee) ) {
			$res = [
				'success' => true,
				'message' => $message
			];
		}

		return $res;

	}
}

?>