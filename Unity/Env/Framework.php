<?php

namespace PHPfox\Unity\Env;

class Framework extends \PHPfox\Unity\Objectify\Framework {
	private $_params = [];

	public function views($path) {
		$this->_params['__views'] = $path;

		return $this;
	}

	public function db($host, $user, $password, $database) {
		$this->app->db->__connect($host, $user, $password, $database);

		return $this;
	}

	public function set($params) {
		$this->_params = $params;

		return $this;
	}

	public function get($key) {
		return (isset($this->_params[$key]) ? $this->_params[$key] : '');
	}
}