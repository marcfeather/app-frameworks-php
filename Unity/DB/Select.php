<?php

namespace PHPfox\Unity\DB;

/**
 * Class Select
 * @package PHPfox
 *
 * @method Select from($table)
 * @method Select where($where = [])
 * @method Select limit($limit)
 * @method Select order($order)
 */
class Select {
	private $_resource;
	private $_query;
	private $_select;

	public function __construct(\mysqli $mysqli, $select) {
		$this->_query = new Query($mysqli);
		$this->_select = $select;
		$this->_resource = $mysqli;
	}

	public function all() {
		$rows = [];
		$query = $this->_query->selector($this->_select);
		while ($row = $query->fetch_object()) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function get() {
		$query = $this->_query->selector($this->_select);
		return $query->fetch_object();
	}

	public function __call($method, $args) {
		call_user_func_array([$this->_query, $method], $args);

		return $this;
	}
}