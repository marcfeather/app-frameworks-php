<?php

namespace PHPfox\Unity\DB;

/**
 * Class Db_Query
 * @package PHPfox
 *
 * @method Query table($table)
 */
class Query {
	private $where;
	private $table;
	private $limit;
	private $order;
	private $resource;

	public function __construct(\mysqli $mysql) {
		$this->resource = $mysql;
	}

	public function from($table) {
		$this->table($table);

		return $this;
	}

	public function __call($method, $args) {
		$this->{$method} = (isset($args[0]) ? $args[0] : '');

		return $this;
	}

	public function where($where = []) {
		if (is_array($where) && count($where))
		{
			// d($where);
			foreach ($where as $sKey => $sValue)
			{
				if (is_string($sKey)) {
					// $this->where .= $sKey . ' = \'' . Phpfox_Database::instance()->escape($sValue) . '\'';
					$this->where .= $this->_where($sKey, $sValue);

					continue;
				}
				$this->where .= $sValue . ' ';
			}

			$this->where = "WHERE " . trim(preg_replace("/^(AND|OR)(.*?)/i", "", trim($this->where)));
		}
		else
		{
			if (!empty($where))
			{
				$this->where .= 'WHERE ' . $where;
			}
		}

		return $this;
	}

	public function sql($query) {
		$resource = $this->resource->query($query);
		if ($resource === false) {
			throw new \RuntimeException('Query error: ' . $this->resource->error);
		}

		return $resource;
	}

	public function selector($select) {
		$sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' ';
		$sql .= $this->where;

		if ($this->order !== null) {
			$sql .= ' ORDER BY ' . $this->order;
		}

		if ($this->limit !== null) {
			$sql .= ' LIMIT ' . $this->limit;
		}

		return $this->sql($sql);
	}

	public function set($keys) {
		$sql = '';
		foreach ($keys as $key => $value) {
			$sql .= ' ' . $key . ' = \'' . $this->resource->escape_string($value) . '\', ';
		}
		$sql = rtrim($sql, ', ');

		return $sql;
	}

	protected function _where($sKey, $mValue) {
		if (is_array($mValue)) {
			$sWhere = 'AND ' . $sKey . '';
			$sKey = array_keys($mValue)[0];
			$sValue = array_values($mValue)[0];
			$sKey = strtolower($sKey);
			switch ($sKey) {
				case '=':
					$sWhere .= ' = ' . $sValue . ' ';
					break;
				case 'in':
					$sWhere .= ' IN(' . $mValue[$sKey] . ')';
					break;
			}

			return $sWhere;

		}
		$sWhere = 'AND ' . $sKey . ' = \'' . $this->resource->real_escape_string($mValue) . '\' ';

		return $sWhere;
	}
}
