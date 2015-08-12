<?php

namespace PHPfox\Unity\Form;

class Textarea extends Framework {
	public function __make($type, $name, $title = null) {
		$this->__build($title, '<textarea name="' . $name . '"></textarea>');
	}
}


