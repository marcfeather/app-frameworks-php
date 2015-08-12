<?php

namespace PHPfox\Unity\Form;

/**
 * Class Form
 * @package PHPfox
 *
 * @method Input text($name)
 * @method Input email($name)
 * @method Input file($name, $title)
 * @method Textarea textarea($name, $title)
 */
class Framework extends \PHPfox\Unity\Objectify\Framework {
	private static $_html = '';

	protected function __build($title = null, $input) {
		if ($title === null) {
			self::$_html .= $input;
		}
		else {
			self::$_html .= '<div class="table">';
			self::$_html .= '<div class="table_left">' . $title . '</div>';
			self::$_html .= '<div class="table_right">' . $input . '</div>';
			self::$_html .= '</div>';
		}
	}

	public function success($callback) {
		if ($this->app->request->method() != 'POST') {
			return false;
		}

		$this->app->route->end(call_user_func($callback, $this->app));

		return true;
	}

	public function submit() {
		return $this->__call('submit', func_get_args());
	}

	public function make() {
		return self::$_html;
	}

	public function __call($method, $args) {
		$type = $method;
		switch ($method) {
			case 'text':
			case 'email':
			case 'password':
			case 'file':
				$type = 'input';
				break;
		}

		if ($type == 'submit') {
			self::$_html .= '<div class="table_clear"><input type="submit" class="button" value="' . $args[0] . '"></div>';
		}
		else {
			$object = (new \ReflectionClass('\\PHPfox\\Unity\\Form\\' . $type))->newInstanceWithoutConstructor();

			call_user_func_array([$object, '__make'], array_merge([$method], $args));
		}

		return $this;
	}
}

