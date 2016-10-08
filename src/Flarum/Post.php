<?php

namespace Infonesy\Driver\Flarum;

class Post extends \B2\Obj
{
	static function find_or_create($data)
	{
		$infonesy_post = Adapter\Post::find(['infonesy_uuid' => $data['UUID']])->first();

		// If post found in adapter database
		if($infonesy_post->is_not_null())
			return self::loader($infonesy_post, NULL, $data);

		$author = User::find_or_create($data['Author']);

		// Make flarum post
		$flarum_post = self::create([
			'topic_id'		=> config('flarum.quarantine.topic_id'),
			'author_id'		=> $author->id(),
			'text'			=> $data['Text'],
			'create_time'	=> strtotime($data['Date']),
		]);

		// Make adapter link
		$infonesy_post = Adapter\Post::create([
			'flarum_post_id' => $flarum_post->id(),
			'infonesy_uuid' => $data['UUID'],
		]);

		return self::loader($infonesy_post, $flarum_post, $data);
	}

	static function loader($infonesy_post, $flarum_post, $data)
	{
		if(!$flarum_post)
			$flarum_post = B2Model\Post::load($infonesy_post->flarum_post_id());

		return $flarum_post;
	}

	static function create($data)
	{
		$app = App::instance();
		$flarum_app = $app->flarum_app;
		$flarum_actor = \Flarum\Core\User::find(popval($data, 'author_id'));
		$flarum_discussion = \Flarum\Core\Discussion::find(popval($data, 'topic_id'));

		$flarum_data = [
			'attributes' => [
				'content' => $data['text'],
				'time' => \Carbon\Carbon::createFromTimestamp($data['create_time'])->toDateTimeString(),
				'is_approved' => true,
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

		$flarum_original_post = $handler->handle($cmd);

		$flarum_b2_post = B2Model\Post::load($flarum_original_post->id);
		return $flarum_b2_post;
	}
}
