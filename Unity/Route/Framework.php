<?php

namespace PHPfox\Unity\Route;

/**
 * Class Route
 * @package PHPfox
 *
 * @method Framework on($route, $callback)
 */
class Framework extends \PHPfox\Unity\Objectify\Framework {
	public function __call($method, $args) {
		$uri = trim($this->app->url->uri(), '/');
		$route = trim($args[0], '/');

		if ($uri == $route) {
			try {
				$this->end(call_user_func($args[1], $this->app));
			} catch (\Exception $e) {
				$this->end([
					'error' => '<div class="error_message">' . $e->getMessage() . '</div>'
				]);
			}
		}
	}

	public function end($content) {
		if (is_string($content)) {
			header('Content-type: text/html; charset=utf-8');
			echo $content;
		}
		else {
			if ($content instanceof \PHPfox\Unity\JS\Query) {
				$query = (string) $content;
				$content = [
					'run' => $query
				];
			}

			header('Content-type: application/json; charset=utf-8');
			echo json_encode($content, JSON_PRETTY_PRINT);
		}
		exit;
	}
}