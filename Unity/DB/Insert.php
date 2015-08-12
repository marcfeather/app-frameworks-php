<?php

namespace PHPfox\Unity\DB;

class Insert {
	private $_resource;
	private $_query;
	private $_set;

	public function __construct(\mysqli $mysqli, $set) {
		$this->_query = new Query($mysqli);
		$this->_set = $set;
		$this->_resource = $mysqli;
	}

	public function in($table) {
		$this->_query->sql('INSERT INTO ' . $table . ' SET ' . $this->_query->set($this->_set));

		return $this->_resource->insert_id;
	}
}

