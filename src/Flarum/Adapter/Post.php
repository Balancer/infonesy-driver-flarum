<?php

namespace Infonesy\Driver\Flarum\Adapter;

class Post extends ObjectDb
{
	function table_name() { return 'posts'; }

	function table_fields()
	{
		return [
			'id',
			'infonesy_uuid',
			'flarum_post_id',
		];
	}
}
