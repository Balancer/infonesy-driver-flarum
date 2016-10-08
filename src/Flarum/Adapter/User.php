<?php

namespace Infonesy\Driver\Flarum\Adapter;

class User extends ObjectDb
{
	function table_name() { return 'users'; }

	function table_fields()
	{
		return [
			'id',
			'infonesy_uuid',
			'flarum_user_id',
			'email_md5',
		];
	}
}
