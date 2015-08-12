<?php

namespace PHPfox\Unity\User;

class Framework extends \PHPfox\Unity\Objectify\Framework {
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

	public function __construct(\PHPfox\Unity\App\Framework $app) {
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