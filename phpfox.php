<?php

ob_start();

require(__DIR__ . '/vendor/autoload.php');

spl_autoload_register(function($class) {
	$class = str_replace("\\", '/', strtolower($class));
	if (substr($class, 0, 12) == 'phpfox/unity') {
		$class = str_replace('phpfox/', '', $class);
		$class = str_replace(' ', '/', ucwords(str_replace('/', ' ', $class)));
		$path = __DIR__ . '/' . $class . '.php';

		require($path);
	}
});