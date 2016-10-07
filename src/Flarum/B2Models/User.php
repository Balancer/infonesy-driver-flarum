<?php

namespace Infonesy\Drivers\Flarum\B2Models;

class User extends ObjectDb
{
	function table_name() { return 'flarum_users'; }

	function table_fields()
	{
		return [
			'id',
			'title' => 'username',
			'email',
			'is_activated',
			'password',
			'bio',
			'avatar_path',
			'preferences',
			'create_time' => 'join_time',
			'last_seen_time',
			'read_time',
			'notifications_read_time',
			'discussions_count',
			'comments_count',
			'flags_read_time',
			'suspend_until',
			'twitter_id',
		];
	}
}
