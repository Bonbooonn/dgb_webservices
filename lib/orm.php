<?php

class Orm {

	protected $db;
	public $_conn = [];
	private $_table;
	private $pdo_obj = [];

	private $columns = [];
	private $result_columns = [];
	private $order_columns = [];

	private $values = [];
	private $placeholders = [];
	private $result = [];
	private $_limit = null;
	private $_offset = null;

	private $joins = [];

	private $_group_columns = [];

	private $select_query = "";
	private $main_table_constraint = "";
	private $LATEST_QUERY = "";
	private $fetch_type = PDO::FETCH_ASSOC;

	public function __construct($db, $table = "") {
		$this->db = $db;
		$this->_table = $table;
		$this->_conn = $this;
	}

	public function dbConn() {
		return $this->db;
	}

	public function initConn() {
		$this->clearVars();
		$this->_conn = $this;
	}

	public function getConn() {
		$this->_conn = $this;
		return $this;
	}

	public function get_latest_query() {
		return $this->LATEST_QUERY;
	}

	private function getLastInsertedRow() {
		$last_id = $this->db->lastInsertId();
		$data = $this->where('id', $last_id)->get_one_result();
		return $data;
	}

	public function delete($conditions) {

		foreach ($conditions as $cond_key => $cond_value) {
			$condition_values[":" . $cond_key] = $cond_value;
			$condition_placeholders[] = ":" . $cond_key;
			$condition_columns[] = $cond_key;
		}

		$sql_query = "DELETE FROM {$this->_table} WHERE ";

		$array_keys = array_keys($condition_columns);
		$last_array_key = array_pop($array_keys);
		foreach ( $condition_columns as $key => $val ) {
			$sql_query .= "`{$val}` = " . $condition_placeholders[$key];

			if ( $key != $last_array_key ) {
				$sql_query .= " AND ";
			}

		}

		$pdo_obj = $this->db->prepare($sql_query);
		foreach ( $condition_values as $val_key => $val_value ) {
			$pdo_obj->bindValue($val_key, $val_value);
		}

		if ( $pdo_obj->execute() ) {
			$this->clearVars();

			$this->result = true;
		} else {
			$this->pdo_obj = $pdo_obj;
			$this->result = $this->getErrors();
		}

		return $this->result;

	}

	public function insert($params) {
		foreach ( $params as $key => $val ) {
			$this->columns[] = $key;
			$this->values[":" . $key] = $val;
			$this->placeholders[] = ":" . $key;
		}

		$sql_query = "INSERT INTO {$this->_table} (" . join(", ", $this->columns) . ") VALUES (" . join(", ", $this->placeholders) . ")"; 
		$pdo_obj = $this->db->prepare($sql_query);
		foreach ( $this->values as $key => $val ) {
			$pdo_obj->bindValue($key, $val);
		}

		if ( $pdo_obj->execute() ) {
			$this->clearVars();
			$inserted_row = $this->getLastInsertedRow();
			$this->result = $inserted_row;
		} else {
			$this->pdo_obj = $pdo_obj;
			$this->result = $this->getErrors();
		}

		return $this->result;
	}

	public function update($params, $conditions) {

		foreach ($params as $key => $value) {
			$this->columns[] = $key;
			$this->values[":" . $key] = $value;
			$this->placeholders[] = ":" . $key;
		}

		foreach ($conditions as $cond_key => $cond_value) {
			$condition_values[":" . $cond_key] = $cond_value;
			$condition_placeholders[] = ":" . $cond_key;
			$condition_columns[] = $cond_key;
		}

		$sql_query = "UPDATE `{$this->_table}` SET ";
		$array_keys = array_keys($this->columns);
		$last_array_key = array_pop($array_keys);
		foreach ( $this->columns as $col_key => $col_val ) {
			$sql_query .= "`{$col_val}` = " . $this->placeholders[$col_key];

			if ( $col_key != $last_array_key ) {
				$sql_query .=", ";
			}

		}

		$sql_query .= " WHERE ";
		$array_keys = array_keys($condition_columns);
		$last_array_key = array_pop($array_keys);
		foreach ( $condition_columns as $key => $val ) {
			$sql_query .= "`{$val}` = " . $condition_placeholders[$key];

			if ( $key != $last_array_key ) {
				$sql_query .= " AND ";
			}

		}

		$values = array_merge($this->values, $condition_values);

		$pdo_obj = $this->db->prepare($sql_query);
		foreach ( $values as $val_key => $val_value ) {
			$pdo_obj->bindValue($val_key, $val_value);
		}

		if ( $pdo_obj->execute() ) {
			$this->clearVars();

			$this->result = true;
		} else {
			$this->pdo_obj = $pdo_obj;
			$this->result = $this->getErrors();
		}

		return $this->result;

	}

	/**
	 * [select description]
	 * @param  [String] $column [column name]
	 *         > if column name has a delimiter dot(.)
	 *         > the string before the dot(.) must be the table name
	 *         > the string after the dot(.) must be the column
	 *         > columns that has no delimiter will be read as
	 *         > column in the main table
	 * @param  [String] $alias  [column alias]
	 * @return [class object]
	 */
	public function select($column, $alias = null) {
		$tmp = [];
		$tmp_column = $this->_break_column_identifiers($column);

		if ( !empty($alias) ) {
			$tmp_column .= " AS `{$alias}`";
		}

		$this->result_columns[] = $tmp_column;
		return $this->_conn;
	}

	public function select_expr($expr, $alias) {
		$this->result_columns[] = $expr . " AS `${alias}`";
		return $this->_conn;
	}

	/**
	 * [join description]
	 * @param  [String] $table      [table name]
	 *         > includes what kind of join (INNER, LEFT, RIGHT joins)
	 *         > can put constraints
	 * @param  [String] $conditions [condition of join]
	 * @param  [String] $alias      [table join alias]
	 * @return [class object]
	 */
	public function join($table, $conditions, $alias = null) {

		if ( !empty($alias) ) {
			$this->joins[] = " " . $table . " AS " . $alias . " ON " .  $conditions;
		} else {
			$this->joins[] = " " . $table . " ON " .  $conditions;
		}

		
		return $this->_conn;
	}

	public function showssss() {
		return $this->build_select_query();
	}

	/**
	 * [where description]
	 * @param   [String] $column [column name]
	 *         > if column name has a delimiter dot(.)
	 *         > the string before the dot(.) must be the table name
	 *         > the string after the dot(.) must be the column
	 *         > columns that has no delimiter will be read as
	 *         > column in the main table
	 * @param  [String] $value  [value in where]
	 * @return [class object]
	 */
	public function where($column, $value) {
		$tmp = [];
		$tmp_placeHolder = [];

		if ( strpos($column, ".") !== false ) {
			$tmp = explode(".", $column);

			if ( empty($tmp[0]) || empty($tmp[1]) ) {
				return $this;
			}
			$tmp_placeHolder = ":" . join("", $tmp);
			$where = "`{$tmp[0]}`.`{$tmp[1]}`";
		} else {
			$tmp_placeHolder = ":" . $column;
			$where = "`{$this->_table}`.`{$column}`";
		}

		$this->values[$tmp_placeHolder] = $value;
		$this->placeholders[] = $tmp_placeHolder;
		$this->columns[] = $where;
		return $this->_conn;

	}

	public function where_raw($raw_query) {
		$this->columns[] = $raw_query . "RAW";
		return $this->_conn;
	}

	public function where_not_equal($column, $value) {
		$tmp = [];
		$tmp_placeHolder = [];

		if ( strpos($column, ".") !== false ) {
			$tmp = explode(".", $column);

			if ( empty($tmp[0]) || empty($tmp[1]) ) {
				return $this;
			}
			$tmp_placeHolder = ":" . join("", $tmp);
			$where = "`{$tmp[0]}`.`{$tmp[1]}` <> ";
		} else {
			$tmp_placeHolder = ":" . $column;
			$where = "`{$this->_table}`.`{$column}` <> ";
		}

		$this->values[$tmp_placeHolder] = $value;
		$this->placeholders[] = $tmp_placeHolder;
		$this->columns[] = $where;
		return $this->_conn;
	}

	public function where_like($column, $value) {
		$tmp = [];
		$tmp_placeHolder = [];

		if ( strpos($column, ".") !== false ) {
			$tmp = explode(".", $column);

			if ( empty($tmp[0]) || empty($tmp[1]) ) {
				return $this;
			}
			$tmp_placeHolder = ":" . join("", $tmp);
			$where = "`{$tmp[0]}`.`{$tmp[1]}` LIKE ";
		} else {
			$tmp_placeHolder = ":" . $column;
			$where = "`{$this->_table}`.`{$column}` LIKE ";
		}

		$this->values[$tmp_placeHolder] = $value . "%";
		$this->placeholders[] = $tmp_placeHolder;
		$this->columns[] = $where;
		return $this->_conn;
	}

	public function where_is_not_null($column) {
		$tmp = [];
		$tmp_placeHolder = [];
		if ( strpos($column, ".") !== false ) {
			$tmp = explode(".", $column);

			if ( empty($tmp[0]) || empty($tmp[1]) ) {
				return $this;
			}
			$tmp_placeHolder = ":" . join("", $tmp);
			$where = "`{$tmp[0]}`.`{$tmp[1]}` IS NOT NULL";
		} else {
			$tmp_placeHolder = ":" . $column;
			$where = "`{$this->_table}`.`{$column}` IS NOT NULL";
		}

		$this->values[$tmp_placeHolder] = null;
		$this->placeholders[] = null;
		$this->columns[] = $where;
		return $this->_conn;
	}

	public function where_is_null($column) {
		$tmp = [];
		$tmp_placeHolder = [];
		if ( strpos($column, ".") !== false ) {
			$tmp = explode(".", $column);

			if ( empty($tmp[0]) || empty($tmp[1]) ) {
				return $this;
			}
			$tmp_placeHolder = ":" . join("", $tmp);
			$where = "`{$tmp[0]}`.`{$tmp[1]}`";
		} else {
			$tmp_placeHolder = ":" . $column;
			$where = "`{$this->_table}`.`{$column}`";
		}

		$this->values[$tmp_placeHolder] = null;
		$this->placeholders[] = null;
		$this->columns[] = $where;
		return $this->_conn;
	}


	/**
	 * [group_by Determine the order in the result set] for single columns only
	 * @param  [String] [column name]
	 *         > if column name has a delimiter dot(.)
	 *         > the string before the dot(.) must be the table name
	 *         > the string after the dot(.) must be the column
	 *         > columns that has no delimiter will be read as
	 *         > column in the main table
	 * @return [class object]
	 */
	public function group_by($column) {
		$tmp_column = $this->_break_column_identifiers($column);

		$this->_group_columns[] = $tmp_column;

		return $this->_conn;
	}

	/**
	 * [group_by_expr Determine the group in the result set] for multiple columns only
	 * @param [String] [column(s) name]
	 * @return [class object]
	 */
	public function group_by_expr($column) {
		$this->_group_columns[] = $column;
		return $this->_conn;
	}

	public function no_group() {
		$this->_group_columns = [];
		return $this->_conn;
	}

	/**
	 * [order_by Determine the order in the result set] for single columns only
	 * @param  [String] [column name]
	 *         > if column name has a delimiter dot(.)
	 *         > the string before the dot(.) must be the table name
	 *         > the string after the dot(.) must be the column
	 *         > columns that has no delimiter will be read as
	 *         > column in the main table
	 * @param [String] [DESC, ASC] (Defaul ASC)
	 * @return [class object]
	 */
	public function order_by($column, $order_type = "ASC") {
		$tmp = [];
		$tmp_column = $this->_break_column_identifiers($column);

		$this->order_columns[] = $tmp_column . $order_type;

		return $this->_conn;

	}

	/**
	 * [order_by_exp Determine the order in the result set] for multiple columns only
	 * @param [String] [column(s) name]
	 * @return [class object]
	 */
	public function order_by_expr($column) {
		$this->_order_columns[] = $column;
		return $this->_conn;
	}

	public function limit($limit) {
		$this->_limit = $limit;
		return $this->_conn;
	}

	public function no_limit() {
		$this->_limit = null;
		return $this->_conn;
	}

	public function offset($offset) {
		$this->_offset = $offset;
		return $this->_conn;
	}

	public function no_offset() {
		$this->_offset = null;
		return $this->_conn;
	}

	public function add_main_table_constraint($constraint) {
		$this->main_table_constraint = $constraint;
		return $this->_conn;
	}

	public function get_result_set() {

		$exec = $this->_run_select();

		if ( $exec ) {
			$response = $this->pdo_obj->fetchAll($this->fetch_type);

			$this->result = [
				'success' => true,
				'result' => $response
			];
		} else {
			$this->result = $this->getErrors();
		}

		return $this->result;
	}

	public function getQuery() {
		return $this->build_select_query();
	}

	public function get_one_result() {
		$exec = $this->_run_select_one();

		if ( $exec ) {
			$response = $this->pdo_obj->fetch($this->fetch_type);
			$this->result = $response;
		} else {
			$this->result = $this->getErrors();
		}

		return $this->result;
	}

	private function build_limit() {

		if ( empty($this->_limit) ) {
			return null;
		}

		return " LIMIT " . $this->_limit;
	}

	private function build_offset() {
		
		if ( strlen($this->_offset) <= 0 ) {
			return null;
		}

		return " OFFSET " . $this->_offset;
	}

	private function _run_select_one() {
		$sql_query = $this->build_select_query() . " LIMIT 1";
		$values = $this->values;
		$placeholders = $this->placeholders;
		$pdo_obj = $this->db->prepare($sql_query);
		foreach ( $values as $val_key => $value_val ) {

			if ( $value_val != NULL ) {
				$pdo_obj->bindValue($val_key, $value_val);
			}

		}
		$this->LATEST_QUERY = $pdo_obj->queryString;
		$this->pdo_obj = $pdo_obj;
		return $pdo_obj->execute();
	}

	private function _run_select() {
		$sql_query = $this->build_select_query();
		$values = $this->values;
		$placeholders = $this->placeholders;
		$pdo_obj = $this->db->prepare($sql_query);
		foreach ( $values as $val_key => $value_val ) {

			if ( $value_val != NULL ) {
				$pdo_obj->bindValue($val_key, $value_val);
			}

		}
		$this->LATEST_QUERY = $this->build_select_shows();
		$this->pdo_obj = $pdo_obj;
		return $pdo_obj->execute();
	}

	private function build_select_shows() {
		$sql_query = $this->build_select_query();
		$values = $this->values;
		foreach ( $values as $val_key => $value_val ) {

			if ( $value_val != NULL ) {
				$sql_query = str_replace($val_key, $value_val, $sql_query);
			}

		}

		return $sql_query;

	}

	private function build_select_query() {
		$result_columns = $this->result_columns;

		if ( !empty($this->main_table_constraint) ) {
			$this->_table .= " " . $this->main_table_constraint;
		}

		if ( empty($result_columns) ) {
			$select_query = "SELECT * FROM {$this->_table}";
		} else {
			$select_query = "SELECT " . join(", ", $result_columns) . " FROM {$this->_table}";
		}

		$where = $this->build_where();
		$joins = $this->build_joins();
		$group = $this->build_group();
		$order = $this->build_order();
		$limit = $this->build_limit();
		$offset = $this->build_offset();
		$this->select_query = $select_query . $joins . $where . $group . $order . $limit . $offset;
		return $this->select_query;
	}

	private function build_joins() {
		$joins = "";
		$join_table = $this->joins;
		if ( empty($join_table) ) {
			return null;
		}

		foreach ( $join_table as $key => $val ) {
			$joins .= $val;
		}

		return $joins;
	}

	private function build_where() {
		$where_columns = $this->columns;
		$where_place_holders = $this->placeholders;
		if ( empty($where_columns) ) {
			return null;
		}

		$array_keys = array_keys($where_columns);
		$last_array_key = array_pop($array_keys);

		$where = " WHERE ";
		foreach ( $where_columns as $col_key => $col_val ) {

			if ( strpos($col_val, "<>") > 0 ) {
				$where .= "{$col_val} {$where_place_holders[$col_key]}";
			} else if ( preg_match("/RAW/i", $col_val) ) {
				$where .= str_replace("RAW", ' ', $col_val);
			} else if ( strpos($col_val, "LIKE") ) {
				$where .= "{$col_val} {$where_place_holders[$col_key]}";
			} else {
				if ( !empty($where_place_holders[$col_key]) ) {
					$where .= "{$col_val} = {$where_place_holders[$col_key]}";
				} else if ( strpos($col_val, "IS NOT NULL") > 0 ) {
					$where .= " {$col_val} ";
				} else {
					$where .= "{$col_val} IS NULL";
				}
			}

			if ( $last_array_key != $col_key ) {
				$where .= " AND ";
			}
		}

		return $where;
	}

	private function build_order() {
		$order_columns = $this->order_columns;
		$order = " ORDER BY ";

		if ( empty($order_columns) ) {
			return null;
		}

		foreach ( $order_columns as $order_key => $order_val ) {
			$order .= " {$order_val} ,";
		}

		$order = substr($order, 0, -1);
		return $order;
	}

	private function build_group() {
		$group_colums = $this->_group_columns;
		$group = " GROUP BY ";

		if ( empty($group_colums) ) {
			return null;
		}

		foreach ( $group_colums as $g_key => $g_val ) {
			$group .= " {$g_val} ,";
		}

		$group = substr($group, 0, -1);
		return $group;
	}

	/**
	 * [_break_column_identifiers description]
	 * @param  [String] [column name]
	 * @return [String] [column]
	 */
	private function _break_column_identifiers($column) {
		$col = "";
		if ( strpos($column, ".") !== false ) {
			$tmp = explode(".", $column);

			if ( empty($tmp[0]) || empty($tmp[1]) ) {
				return $this;
			}

			$col =  "`{$tmp[0]}`.`{$tmp[1]}`";

		} else {
			$col = "`{$this->_table}`.`{$column}`";
		}

		return $col;
	}

	private function getErrors() {
		$errors = $this->pdo_obj->errorInfo();
		$response = [
			'failed' => true,
			'message' => $errors
		];
		return $response;
	}

	private function clearVars() {
		$this->columns = [];
		$this->values = [];
		$this->placeholders = [];
		$this->pdo_obj = [];
		$this->result = [];
		$this->fetch_type = PDO::FETCH_ASSOC;
		$this->select_query = "";
		$this->result_columns = [];
		$this->_group_columns = [];
		$this->joins = [];
		$this->_limit = null;
		$this->_offset = null;

	}

	private function clean() {
		$this->clearVars();
		$this->db = [];
		$this->_table = [];
		$this->_conn = [];
	}

	function __destruct() {
		$this->clean();
	}

}

?>