<?php

namespace PHPfox\Unity\Request;

class Framework extends \PHPfox\Unity\Objectify\Framework {
	public function get($key) {
		return (isset($_REQUEST[$key]) ? $_REQUEST[$key] : '');
	}

	public function method() {
		return (isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET');
	}
}