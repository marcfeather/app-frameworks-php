<?php

namespace PHPfox\Unity\DB;

class Framework extends \PHPfox\Unity\Objectify\Framework {
	/**
	 * @var \mysqli
	 */
	private static $resource;

	public function select($select) {
		return new Select(self::$resource, $select);
	}

	public function insert($set) {
		return new Insert(self::$resource, $set);
	}

	public function __connect($host, $user, $password, $database) {
		self::$resource = new \mysqli($host, $user, $password, $database);
	}
}