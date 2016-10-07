<?php

namespace Infonesy\Drivers\Flarum\B2Models;

class ObjectDb extends \bors_object_db
{
	function storage_engine() { return \bors_storage_mysql::class; }
	function db_name() { return 'FLARUM'; }
}
