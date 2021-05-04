<?php

class Holiday extends \BaseModel {

	public function __construct($db) {
		parent::__construct($db, 'holidays');
	}

	public function add_holiday($params) {

		$dt = new DateTime();
		$date = $dt->format(DEFAULT_DATE_TIME_FORMAT);
		$year = $dt->format('Y');

		$insert = [];
		$res = [];

		$holiday = !empty($params['holiday']) ? ucfirst(strtolower($params['holiday'])) : "";
		if ( empty($holiday) ) {
			return $this->return_message(false, 'Please input a Holiday');
		}
		// check holiday
		$check_holiday = $this->appOrm
			->select('id')
			->where('year', $year)
			->where('holiday', $holiday)
			->get_one_result();

		if ( !empty($check_holiday) ) {
			return $this->return_message(false, "Holiday already added in the current year - {$year}");
		}

		if ( !empty($holiday) ) {
			$insert['holiday'] = $holiday;
		}

		if ( !empty($params['holiday_date']) ) {
			$hy_date = new DateTime($params['holiday_date']);
			$holiday_date = $hy_date->format(DEFAULT_DATE_FORMAT);
			$insert['holiday_date'] = $holiday_date;
		}

		if ( !empty($params['holiday_type']) ) {
			$insert['holiday_type'] = $params['holiday_type'];
		} 

		$insert['year'] = $year;
		if ( empty($params['holiday_id']) ) {
			$insert['created'] = $date;
			$hy = $this->getOrm()->insert($insert);
		} else {
			$insert['modified'] = $date;
			$hy = $this->getOrm()->update($insert, ['id' => $params['holiday_id']]);
		}

		if ( empty($hy) ) {
			return $this->return_message(false, "Failed to add holiday : {$holiday}");
		}

		return $this->return_message(true, "Holiday added successfully!");

	}

	public function search_holiday($params) {

		$page = !empty($params['page']) ? $params['page'] : DEFAULT_PAGE;
		$per_page = !empty($params['per_page']) ? $params['per_page'] : DEFAULT_PER_PAGE;
		
		$limit = $per_page;
		$offset = (($page - 1) * $per_page);

		$holiday_expr = 'CASE WHEN holiday_type = ' . REGULAR . ' THEN "Regular" WHEN holiday_type = ' . SPECIAL_NON_WORKING . ' THEN "Special Non Working" WHEN holiday_type = ' . SPECIAL_WORKING . ' THEN "Special Working" END';

		$this->appOrm
			->select('id', 'holiday_id')
			->select('holiday')
			->select('holiday_date')
			->select_expr($holiday_expr, 'holiday_type');

		if ( !empty($params['year']) ) {
			$this->appOrm
				->where('year', $params['year']);
		}

		if ( !empty($params['holiday']) ) {
			$holiday = !empty($params['holiday']) ? ucfirst(strtolower($params['holiday'])) : "";
			$this->appOrm
				->where('holiday', $holiday);
		}

		if ( !empty($params['is_paginate']) ) {

			$hy_res = $this->appOrm
				->limit($limit)
				->offset($offset)
				->order_by('holiday_date', 'ASC')
				->get_result_set();

			$total_count = $this->appOrm
				->select_expr('COUNT(holidays.id)', 'total_count')
				->no_limit()
				->no_offset()
				->no_group()
				->get_one_result();

			if ( !empty($hy_res['success']) ) {
				if ( count($hy_res['result']) == 1 ) {
					if ( !empty($hy_res['result'][0]['holiday_id']) ) {
						$res['list'] = $hy_res['result'];
					}
				} else {
					$res['list'] = $hy_res['result'];
				}
			}

			$res['total_count'] = !empty($total_count['total_count']) ? $total_count['total_count'] : 0;

		} else {
			$hy_res = $this->appOrm
				->order_by('holiday_date', 'ASC')
				->get_result_set();

			if ( !empty($hy_res['success']) ) {
				if ( count($hy_res['result']) == 1 ) {
					if ( !empty($hy_res['result'][0]['holiday_id']) ) {
						$res['list'] = $hy_res['result'];
					}
				} else {
					$res['list'] = $hy_res['result'];
				}
			}
		}

		return $res;
	}

}

?>