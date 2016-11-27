<?php

namespace Infonesy\Driver\Flarum;

class Map extends \bors_object_db
{
	function storage_engine() { return \bors_storage_mysql::class; }
	function db_name() { return config('flarum_db'); }

    function table_name() { return 'infonesy_map'; }

    function table_fields()
    {
        return [
            'id',
            'uuid',
            'target_class_name',
            'target_id',
            'container_uuid',
            'author_uuid',
        ];
    }
}
