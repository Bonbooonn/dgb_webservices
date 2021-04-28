<?php

class Designation extends \BaseModel {
	public function __construct($db) {
		parent::__construct($db, 'designations');
	}

	public function add_designation($params) {

		if ( empty($params['designation']) ) {
			return [
				'success' => false,
				'message' => "Designation is empty"
			];
		}

		$date = date(DEFAULT_DATE_TIME_FORMAT);
		$insert = [];
		$res = [];
		$message = "";

		$designation = ucfirst(strtolower($params['designation']));

		$check_designation = $this->getOrm()
			->select('id')
			->where('designation', $designation)
			->get_one_result();

		if ( !empty($check_designation) ) {
			return [
				'success' => false,
				'message' => "Designation already exists!"
			];
		}

		$insert['designation'] = $designation;
		$is_update = false;
		$this->getOrm()->initConn();
		if ( !empty($params['designation_id']) ) {
			$message = "Designation Updated!";
			$insert['modified'] = $date;
			$ds = $this->getOrm()->update($insert, ['id' => $params['designation_id']]);
			$is_update = true;
		} else {
			$insert['created'] = $date;
			$ds = $this->getOrm()->insert($insert);
			$message = "Designation Added!";
		}

		if ( !empty($ds) ) {
			$res = [
				'success' => true,
				'message' => $message,
				'is_update' => $is_update
			];
		}

		return $res;
	}

	public function search_designation($params) {
		$page = !empty($params['page']) ? $params['page'] : DEFAULT_PAGE;
		$per_page = !empty($params['per_page']) ? $params['per_page'] : DEFAULT_PER_PAGE;
		$limit = $per_page;
		$offset = (($page - 1) * $per_page);

		$this->appOrm
			->select('designations.id', 'designation_id')
			->select('designations.designation')
			->select_expr('COUNT(CASE WHEN employees.status = 1 THEN employees.designation_id ELSE null END)', 'ee_count')
			->join('LEFT OUTER JOIN employees', 'employees.designation_id = designations.id')
			->group_by('designations.id');

		if ( !empty($params['designation']) ) {
			$designation = ucfirst(strtolower($params['designation']));
			$this->appOrm->where_like('designations.designation', $designation);
		}

		$ds_res = $this->appOrm
			->limit($limit)
			->offset($offset)
			->get_result_set();

		$total_count = $this->appOrm
			->select_expr('COUNT(designations.id)', 'total_count')
			->no_limit()
			->no_offset()
			->no_group()
			->get_one_result();

		$res = [];
		if ( !empty($ds_res['success']) ) {
			if ( count($ds_res['result']) == 1 ) {
				if ( !empty($ds_res['result'][0]['designation_id']) ) {
					$res['list'] = $ds_res['result'];
				}
			} else {
				$res['list'] = $ds_res['result'];
			}
		}

		$res['total_count'] = !empty($total_count['total_count']) ? $total_count['total_count'] : 0;

		return $res;

	}

	public function get_designation_details($params) {
		if ( empty($params['designation_id']) ) {
			return [];
		}

		$res = $this->appOrm
			->select('designation')
			->where('id', $params['designation_id'])
			->get_one_result();

		return $res;
	}

	public function delete_designation($params) {
		if ( empty($params['designation_id']) ) {
			return [
				'success' => false,
				'message' => "Failed to delete designation"
			];
		}

		$res = $this->getOrm()->delete(['id' => $params['designation_id']]);


		if ( $res ) {
			return [
				'success' => true,
				'message' => "Designation Deleted"
			];
		}

		return [
			'success' => false,
			'message' => "An error occured"
		];
	}
}

?>