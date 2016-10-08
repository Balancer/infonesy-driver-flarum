<?php

namespace Infonesy\Driver\Flarum\Adapter;

class ObjectDb extends \bors_object_db
{
	function storage_engine() { return \bors_storage_sqlite::class; }
	function db_name() { return config('flarum_adapter_db', COMPOSER_ROOT.'/data/infonesy-driver-flarum.sqlite'); }
}
