<?php

namespace Infonesy\Driver;

class Flarum
{
	static function bors_config_host_init()
	{
		\B2\Cfg::set('flarum.root', COMPOSER_ROOT);

		$cfg = require \B2\Cfg::get('flarum.root').'/config.php';

		$db = $cfg['database'];

		\B2\Cfg::set('flarum.db', $db['database']);
		\B2\Cfg::set('flarum.db.prefix', $db['prefix']);

		mysql_access(\B2\Cfg::get('flarum.db'), $db['username'], $db['password'], $db['host']);
	}
}
