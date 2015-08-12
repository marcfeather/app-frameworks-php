<?php

namespace PHPfox\Unity\Page;

class Framework extends \PHPfox\Unity\Objectify\Framework {
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	private $title = '';

	public function boot() {
		$loader = new \Twig_Loader_Filesystem();
		$loader->addPath($this->app->env->get('__views'));

		$this->twig = new \Twig_Environment($loader, [
			'cache' => false,
			'autoescape' => false
		]);
	}

	public function title($title) {
		$this->title = $title;

		return $this;
	}

	public function render($file, $params = []) {
		if (!$this->twig) {
			$this->boot();
		}

		/*
		$path = __DIR__ . '/../views/';
		if (!is_dir($path)) {
			$this->__fatal('Missing "/views/" folder.');
		}

		$path = $path . $file;
		if (!file_exists($path)) {
			$this->__fatal('HTML view is missing: ' . $file);
		}

		$cache = __DIR__ . '/../storage/';
		if (@!is_writable($cache)) {
			$this->__fatal('Unable to write to "/storage/" directory.');
		}

		$cache .= 'views/';
		if (!is_dir($cache)) {
			mkdir($cache);
		}
		*/

		/*
		$cached = $cache . $file . '.php';
		$content = file_get_contents($path);
		if ($params) {
			foreach ($params as $key => $value) {
				self::$_params[$key] = $value;

				$content = preg_replace_callback('/{{ ([a-zA-Z0-9]+) }}/is', function($matches) {
					return "{\$this->__param('{$matches[1]}')}";
				}, $content);
			}
		}

		$content = str_replace('"', '\\"', $content);
		$className = 'PHPfox_Views_' . md5($file);
		$class = "<?php \n";
		$class .= 'class ' . $className . ' extends \\PHPfox\\Page { public function get() {
			return "' . $content . '";
		} }';

		file_put_contents($cached, $class);

		require($cached);

		$reflection = (new \ReflectionClass($className))->newInstanceWithoutConstructor();

		return $reflection->get();
		*/

		$params['title'] = $this->title;

		return $this->twig->render($file, $params);
	}
}