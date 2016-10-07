<?php

namespace Infonesy\Driver\Flarum;

class FlarumServerAdapter extends \Flarum\Forum\Server
{
	public function getFlarumApp()
	{
		$app = $this->getApp();
		return $app;
	}
}

class App
{
	var $flarum_server;
	var $flarum_app;

	static function instance()
	{
		static $instance;
		if(empty($instance))
		{
			require FLARUM_DIR.'/vendor/autoload.php';
			$app = new App;
			$app->flarum_server = new FlarumServerAdapter(FLARUM_DIR);
			$app->flarum_app = $app->flarum_server->getFlarumApp();
			$instance = $app;
		}

		return $instance;
	}
}