<?php

namespace PHPfox\Unity\JS;

class Framework extends \PHPfox\Unity\Objectify\Framework {
	public function query($selectors) {
		return new Query($selectors);
	}
}