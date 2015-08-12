<?php

namespace PHPfox\Unity\Url;

class Framework extends \PHPfox\Unity\Objectify\Framework {

	public function uri() {
		return '/' . trim($_SERVER['HTTP_API_URI'], '/');
	}

	public function endpoint() {
		return $_SERVER['HTTP_API_ENDPOINT'];
	}

	public function home() {
		return $_SERVER['HTTP_API_HOME'];
	}

	public function make($uri) {
		return $this->home() . trim($uri, '/');
	}
}