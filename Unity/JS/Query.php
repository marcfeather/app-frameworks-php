<?php

namespace PHPfox\Unity\JS;

/**
 * Class JS
 * @package PHPfox
 *
 * @method Framework html($html)
 * @method Framework prepend($html)
 */
class Query {
	private $query;

	public function __construct($selectors) {
		$this->query = '$(\'' . $selectors . '\')';
	}

	public function __call($method, $args) {
		$param = '';
		switch ($method) {
			case 'html':

				break;
		}

		$param = $args[0];

		$this->query .= '.' . $method . '("' . str_replace(['"', "\n", "\t"], ["[PF_DOUBLE_QUOTE]", '', ''], $param) . '")';

		return $this;
	}

	public function __toString() {
		return $this->query . '; $Core.loadInit();';
	}
}

