<?php

namespace Infonesy\Driver\Flarum;

class Post extends \B2\Obj
{
	var $flarum_post;

	static function create($data)
	{
		$app = App::instance();
		$flarum_app = $app->flarum_app;
		$flarum_actor = \Flarum\Core\User::find(popval($data, 'author_id'));
		$flarum_discussion = \Flarum\Core\Discussion::find(popval($data, 'topic_id'));

		$flarum_data = [
			'attributes' => [
				'content' => $data['text'],
				'time' => \Carbon\Carbon::now('utc')->toDateTimeString(),
			],
		];

		$ipAddress = popval($data, 'author_ip');

		$cmd = new \Flarum\Core\Command\PostReply($flarum_discussion->id, $flarum_actor, $flarum_data, $ipAddress);
		$handler = new \Flarum\Core\Command\PostReplyHandler(
			$flarum_app->events,
			new	\Flarum\Core\Repository\DiscussionRepository,
			new	\Flarum\Core\Notification\NotificationSyncer(new \Flarum\Core\Repository\NotificationRepository, new \Flarum\Core\Notification\NotificationMailer($flarum_app->mailer)),
			new \Flarum\Core\Validator\PostValidator($flarum_app->validator, $flarum_app->events, $flarum_app->make(\Symfony\Component\Translation\TranslatorInterface::class))
		);

		$flarum_post = $handler->handle($cmd);

		$post = new Post(NULL);
		$post->flarum_post = $flarum_post;
		return $post;
	}

	function id() { return $this->flarum_post->id; }
}
