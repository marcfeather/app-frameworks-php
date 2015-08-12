<?php

namespace PHPfox\Unity\Client;

class Framework extends \PHPfox\Unity\Objectify\Framework {
	public function id() {
		return $_SERVER['HTTP_API_CLIENT_ID'];
	}
}