<?php

namespace PHPfox\Unity\Form;

class Input extends Framework {
	public function __make($type, $name, $title = null) {
		$this->__build($title, '<input type="' . $type . '" name="' . $name . '">');
	}
}