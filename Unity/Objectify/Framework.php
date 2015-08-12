<?php

namespace PHPfox\Unity\Objectify;

abstract class Framework {
	protected $app;

	public function __construct(\PHPfox\Unity\App\Framework $app) {
		$this->app = $app;
	}

	public function __fatal($message) {
		throw new \RuntimeException($message);
	}
}