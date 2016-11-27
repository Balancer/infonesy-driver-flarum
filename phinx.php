<?php

require __DIR__.'/../../../vendor/autoload.php';

bors::init_new();

require_once __DIR__.'/../../../config-host.php';

return [
	'paths' => [
		'migrations' => __DIR__.'/migrations',
//		'seeds' => __DIR__.'/seeds',
	],
	'environments' => [
		'default_database' => 'main',
		'main' => [
			'name' => \B2\Cfg::get('infonesy.map_db'),
			'connection' => driver_mysql::factory(\B2\Cfg::get('infonesy.map_db'))->connection(),
		]
	]
];

