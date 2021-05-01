<?php

class Employee extends \BaseModel {
	public function __construct($db) {
		parent::__construct($db, 'employees');
	}

	public function add_employee($params) {

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

		if ( !empty($params['middle_name']) ) {
			$middle_name = ucfirst(strtolower($params['middle_name']));
			$insert['middle_name'] = $middle_name;
		}

		$this->getOrm()
			->select('id')
			->where('first_name', $first_name)
			->where('last_name', $last_name);

		if ( !empty($middle_name) ) {
			$this->appOrm
				->where('middle_name', $middle_name);
		}

		$check_ee = $this->appOrm->get_one_result();

		if ( !empty($check_ee) && empty($params['employee_id']) ) {
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

			if ( !empty($check_email) && empty($params['employee_id']) ) {
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

			if ( !empty($check_phone) && empty($params['employee_id']) ) {
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
		$is_update = false;
		if ( !empty($params['employee_id']) ) {
			$insert['modified'] = $date;
			$ee = $this->getOrm()->update($insert, ['id' => $params['employee_id']]);
			$message = "Employee Information Updated!";
			$is_update = true;
		} else {
			$insert['created'] = $date;
			$ee = $this->getOrm()->insert($insert);
			$message = "Employee Information Added!";
		}

		if ( !empty($ee) ) {
			$res = [
				'success' => true,
				'message' => $message,
				'is_update' => $is_update
			];
		}

		return $res;

	}

	public function search_employee($params) {

		$page = !empty($params['page']) ? $params['page'] : DEFAULT_PAGE;
		$per_page = !empty($params['per_page']) ? $params['per_page'] : DEFAULT_PER_PAGE;
		
		$limit = $per_page;
		$offset = (($page - 1) * $per_page);

		$this->appOrm
			->select('employees.id', 'ee_id')
			->select_expr('CASE WHEN middle_name IS NOT NULL THEN CONCAT(first_name, " ", middle_name, " ", last_name) ELSE CONCAT(first_name, " ", last_name) END', 'ee_full_name')
			->select('designations.designation')
			->select('employees.email')
			->select('employees.phone')
			->select_expr('CASE WHEN employees.status = 1 THEN "Active" ELSE "Inactive" END', 'status')
			->join('LEFT OUTER JOIN designations', 'designations.id = employees.designation_id');

		if ( !empty($params['first_name']) ) {
			$first_name = ucfirst(strtolower($params['first_name']));
			$this->appOrm
				->where_like('employees.first_name', $first_name);
		}

		if ( !empty($params['last_name']) ) {
			$last_name = ucfirst(strtolower($params['last_name']));
			$this->appOrm
				->where_like('employees.last_name', $last_name);
		}

		if ( !empty($params['email']) ) {
			$this->appOrm
				->where('employees.email', $params['email']);
		}

		if ( !empty($params['phone']) ) {
			$phone_no = $params['phone'];
			$this->appOrm
				->where_like('employees.phone', $phone_no);

		}

		if ( isset($params['status']) && mb_strlen($params['status']) > 0 ) {
			$this->appOrm
				->where('employees.status', $params['status']);
		}

		if ( !empty($params['search_daily_salary']) ) {
			$this->appOrm
				->where('employees.daily_salary', $params['search_daily_salary']);
		}

		if ( !empty($params['search_monthly_salary']) ) {
			$this->appOrm
				->where('employees.monthly_salary', $params['search_monthly_salary']);
		}

		$ee_res = $this->appOrm
			->limit($limit)
			->offset($offset)
			->get_result_set();

		$total_count = $this->appOrm
			->select_expr('COUNT(employees.id)', 'total_count')
			->no_limit()
			->no_offset()
			->no_group()
			->get_one_result();

		$res = [];
		if ( !empty($ee_res['success']) ) {
			if ( count($ee_res['result']) == 1 ) {
				if ( !empty($ee_res['result'][0]['ee_id']) ) {
					$res['list'] = $ee_res['result'];
				}
			} else {
				$res['list'] = $ee_res['result'];
			}
		}

		$res['total_count'] = !empty($total_count['total_count']) ? $total_count['total_count'] : 0;

		return $res;

	}

	public function get_employee_details($params) {

		if ( empty($params['ee_id']) ) {
			return [];
		}

		$res = $this->appOrm
			->select('employees.id', 'ee_id')
			->select('employees.first_name')
			->select('employees.last_name')
			->select('employees.middle_name')
			->select('employees.daily_salary')
			->select('employees.monthly_salary')
			->select('employees.email')
			->select('employees.phone')
			->select('designations.designation')
			->select('designations.id', 'designation_id')
			->select('employees.status')
			->join('INNER JOIN designations', 'designations.id = employees.designation_id')
			->where('employees.id', $params['ee_id'])
			->get_one_result();

		return $res;
	}

	public function delete_employee($params) {
		if ( empty($params['ee_id']) ) {
			return [
				'success' => false,
				'message' => "Failed to delete designation"
			];
		}

		$res = $this->appOrm->delete(['id' => $params['ee_id']]);

		if ( $res ) {
			return [
				'success' => true,
				'message' => "Employee Deleted"
			];
		}

		return [
			'success' => false,
			'message' => "An error occured"
		];
	}
}

?>