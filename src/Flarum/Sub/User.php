<?php

namespace Infonesy\Driver\Flarum\Sub;

class User extends \Flarum\Core\User
{
	var $forced_time = false;

	public function isAdmin()
	{
		return parent::isAdmin() || $this->forced_time;
	}
}
