<?php

namespace PHPfox;

class App {
	/**
	 * @var User
	 */
	public $user;

	/**
	 * @var Form
	 */
	public $form;

	/**
	 * @var Route
	 */
	public $route;

	/**
	 * @var Url
	 */
	public $url;

	/**
	 * @var Page
	 */
	public $page;

	/**
	 * @var Env
	 */
	public $env;

	/**
	 * @var Db
	 */
	public $db;

	/**
	 * @var Request
	 */
	public $request;

	/**
	 * @var Client
	 */
	public $client;

	/**
	 * @var JS
	 */
	public $js;

	/**
	 * @var Date
	 */
	public $date;

	private $_load = [
		'user' => 'User',
		'form' => 'Form',
		'route' => 'Route',
		'url' => 'Url',
		'page' => 'Page',
		'env' => 'Env',
		'db' => 'Db',
		'request' => 'Request',
		'client' => 'Client',
		'js' => 'JS',
		'date' => 'Date'
	];

	public function __construct($callback) {
		foreach ($this->_load as $__key => $class) {
			$this->{$__key} = (new \ReflectionClass('\\PHPfox\\' . $class))->newInstance($this);
		}

		call_user_func($callback, $this);
	}

	public function error($message) {
		throw new \Exception($message);
	}
}

abstract class Objectify {
	protected $app;

	public function __construct(App $app) {
		$this->app = $app;
	}

	public function __fatal($message) {
		throw new \RuntimeException($message);
	}
}

class Date {
	public function __construct() {
		date_default_timezone_set('GMT');
	}

	public function timestamp() {
		return time();
	}
}

/**
 * Class JS
 * @package PHPfox
 *
 * @method JS html($html)
 * @method JS prepend($html)
 */
class JS_Query {
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

class JS extends Objectify {
	public function query($selectors) {
		return new JS_Query($selectors);
	}
}

class Client extends Objectify {
	public function id() {
		return $_SERVER['HTTP_API_CLIENT_ID'];
	}
}

/**
 * Class Db_Query
 * @package PHPfox
 *
 * @method table($table)
 */
class Db_Query {
	private $where;
	private $table;
	private $limit;
	private $order;
	private $resource;

	public function __construct(\mysqli $mysql) {
		$this->resource = $mysql;
	}

	public function from($table) {
		$this->table($table);
	}

	public function __call($method, $args) {
		$this->{$method} = $args[0];
	}

	public function where($where = []) {
		if (is_array($where) && count($where))
		{
			// d($where);
			foreach ($where as $sKey => $sValue)
			{
				if (is_string($sKey)) {
					// $this->where .= $sKey . ' = \'' . Phpfox_Database::instance()->escape($sValue) . '\'';
					$this->where .= $this->_where($sKey, $sValue);

					continue;
				}
				$this->where .= $sValue . ' ';
			}

			$this->where = "WHERE " . trim(preg_replace("/^(AND|OR)(.*?)/i", "", trim($this->where)));
		}
		else
		{
			if (!empty($where))
			{
				$this->where .= 'WHERE ' . $where;
			}
		}

		return $this;
	}

	public function sql($query) {
		$resource = $this->resource->query($query);
		if ($resource === false) {
			throw new \RuntimeException('Query error: ' . $this->resource->error);
		}

		return $resource;
	}

	public function selector($select) {
		$sql = 'SELECT ' . $select . ' FROM ' . $this->table . ' ';
		$sql .= $this->where;

		if ($this->order !== null) {
			$sql .= ' ORDER BY ' . $this->order;
		}

		if ($this->limit !== null) {
			$sql .= ' LIMIT ' . $this->limit;
		}

		return $this->sql($sql);
	}

	public function set($keys) {
		$sql = '';
		foreach ($keys as $key => $value) {
			$sql .= ' ' . $key . ' = \'' . $this->resource->escape_string($value) . '\', ';
		}
		$sql = rtrim($sql, ', ');

		return $sql;
	}

	protected function _where($sKey, $mValue) {
		if (is_array($mValue)) {
			$sWhere = 'AND ' . $sKey . '';
			$sKey = array_keys($mValue)[0];
			$sValue = array_values($mValue)[0];
			$sKey = strtolower($sKey);
			switch ($sKey) {
				case '=':
					$sWhere .= ' = ' . $sValue . ' ';
					break;
				case 'in':
					$sWhere .= ' IN(' . $mValue[$sKey] . ')';
					break;
			}

			return $sWhere;

		}
		$sWhere = 'AND ' . $sKey . ' = \'' . $this->resource->real_escape_string($mValue) . '\' ';

		return $sWhere;
	}
}

/**
 * Class Db_Select
 * @package PHPfox
 *
 * @method Db_Select from($table)
 * @method Db_Select where($where = [])
 * @method Db_Select limit($limit)
 * @method Db_Select order($order)
 */
class Db_Select {
	private $_resource;
	private $_query;
	private $_select;

	public function __construct(\mysqli $mysqli, $select) {
		$this->_query = new Db_Query($mysqli);
		$this->_select = $select;
		$this->_resource = $mysqli;
	}

	public function all() {
		$rows = [];
		$query = $this->_query->selector($this->_select);
		while ($row = $query->fetch_object()) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function get() {
		$query = $this->_query->selector($this->_select);
		return $query->fetch_object();
	}

	public function __call($method, $args) {
		call_user_func_array([$this->_query, $method], $args);

		return $this;
	}
}

class Db_Insert {
	private $_resource;
	private $_query;
	private $_set;

	public function __construct(\mysqli $mysqli, $set) {
		$this->_query = new Db_Query($mysqli);
		$this->_set = $set;
		$this->_resource = $mysqli;
	}

	public function in($table) {
		$this->_query->sql('INSERT INTO ' . $table . ' SET ' . $this->_query->set($this->_set));

		return $this->_resource->insert_id;
	}
}

class Db extends Objectify {
	/**
	 * @var \mysqli
	 */
	private static $resource;

	public function select($select) {
		return new Db_Select(self::$resource, $select);
	}

	public function insert($set) {
		return new Db_Insert(self::$resource, $set);
	}

	public function __connect($host, $user, $password, $database) {
		self::$resource = new \mysqli($host, $user, $password, $database);
	}
}

/**
 * Class Route
 * @package PHPfox
 *
 * @method Route on($route, $callback)
 */
class Route extends Objectify {
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
			if ($content instanceof JS_Query) {
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

class Page extends Objectify {
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

class Env extends Objectify {
	private $_params = [];

	public function views($path) {
		$this->_params['__views'] = $path;
	}

	public function db($host, $user, $password, $database) {
		$this->app->db->__connect($host, $user, $password, $database);
	}

	public function set($params) {
		$this->_params = $params;
	}

	public function get($key) {
		return (isset($this->_params[$key]) ? $this->_params[$key] : '');
	}
}

class Request extends Objectify {
	public function get($key) {
		return (isset($_REQUEST[$key]) ? $_REQUEST[$key] : '');
	}

	public function method() {
		return (isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET');
	}
}

class Url extends Objectify {

	public function uri() {
		return '/' . trim($_SERVER['HTTP_API_URI'], '/');
	}

	public function endpoint() {
		return $_SERVER['HTTP_API_ENDPOINT'];
	}

	public function home() {
		return $_SERVER['HTTP_API_HOME'];
	}

	public function make($uri) {
		return $this->home() . trim($uri, '/');
	}
}

class Form_Input extends Form {
	public function __make($type, $name, $title = null) {
		return $this->__build($title, '<input type="' . $type . '" name="' . $name . '">');
	}
}

class Form_Textarea extends Form {
	public function __make($type, $name, $title = null) {
		return $this->__build($title, '<textarea name="' . $name . '"></textarea>');
	}
}

/**
 * Class Form
 * @package PHPfox
 *
 * @method Form_Input text($name)
 * @method Form_Input email($name)
 * @method Form_Input file($name, $title)
 * @method Form_Textarea textarea($name, $title)
 */
class Form extends Objectify {
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
			$object = (new \ReflectionClass('\\PHPfox\\Form_' . $type))->newInstanceWithoutConstructor();

			call_user_func_array([$object, '__make'], array_merge([$method], $args));
		}

		return $this;
	}
}

class User extends Objectify {
	public $id;
	public $url;
	public $name;
	public $name_link;
	public $photo;
	public $photo_link;
	public $location;
	public $gender;
	public $dob;
	public $group;

	private $_user;

	public function __construct(App $app) {
		parent::__construct($app);

		$this->_user = json_decode($_SERVER['HTTP_API_USER']);
		foreach ($this->_user as $__key => $value) {
			$this->{$__key} = $value;
		}
	}

	public function loggedIn() {
		return (isset($this->_user->id) ? true : false);
	}
}