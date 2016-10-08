<?php

namespace Infonesy\Driver\Flarum\Adapter;

class Topic extends ObjectDb
{
	function table_name() { return 'topics'; }

	function table_fields()
	{
		return [
			'id',
			'infonesy_uuid',
			'flarum_topic_id',
		];
	}
}
