<?php

namespace PHPfox\Unity\App;

final class Framework {
	/**
	 * @var \PHPfox\Unity\User\Framework
	 */
	public $user;

	/**
	 * @var \PHPfox\Unity\Form\Framework
	 */
	public $form;

	/**
	 * @var \PHPfox\Unity\Route\Framework
	 */
	public $route;

	/**
	 * @var \PHPfox\Unity\Url\Framework
	 */
	public $url;

	/**
	 * @var \PHPfox\Unity\Page\Framework
	 */
	public $page;

	/**
	 * @var \PHPfox\Unity\Env\Framework
	 */
	public $env;

	/**
	 * @var \PHPfox\Unity\Db\Framework
	 */
	public $db;

	/**
	 * @var \PHPfox\Unity\Request\Framework
	 */
	public $request;

	/**
	 * @var \PHPfox\Unity\Client\Framework
	 */
	public $client;

	/**
	 * @var \PHPfox\Unity\JS\Framework
	 */
	public $js;

	/**
	 * @var \PHPfox\Unity\Date\Framework
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

	public function __construct($configFile) {
		if (!file_exists($configFile)) {
			exit('Config file is missing.');
		}

		foreach ($this->_load as $__key => $class) {
			$this->{$__key} = (new \ReflectionClass('\\PHPfox\\Unity\\' . $class . '\\Framework'))->newInstance($this);
		}

		$config = require($configFile);

		call_user_func($config, $this);
	}

	public function error($message) {
		throw new \Exception($message);
	}
}