<?php

namespace PHPfox\Unity\Date;

class Framework {
	public function __construct() {
		date_default_timezone_set('GMT');
	}

	public function timestamp() {
		return time();
	}
}