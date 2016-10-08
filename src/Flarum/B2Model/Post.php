<?php

namespace Infonesy\Driver\Flarum\B2Model;

class Post extends ObjectDb
{
	function table_name() { return 'flarum_posts'; }

	function table_fields()
	{
		return [
			'id',
			'topic_id' => 'discussion_id',
			'number',
			'create_time' => 'time',
			'owner_id' => 'user_id',
			'type',
			'content' => ['type' => 'markdown'],
			'modify_time' => 'edit_time',
			'last_editor_id' => 'edit_user_id',
			'hide_time',
			'hide_user_id',
			'ip_address',
			'is_approved',
		];
	}
}
