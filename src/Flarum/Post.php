<?php

namespace Infonesy\Driver\Flarum;

class Post extends \B2\Obj
{
	static function infonesy_import($data)
	{
		dump($data);

		$topic_map = Map::find(['uuid' => $data['TopicUUID']])->first();

		if($topic_map->is_null())
			$topic_id = \B2\Cfg::get('infonesy.quarantine.topic_id');
		else
			$topic_id = $topic_map->target_id();

		$map = Map::find(['uuid' => $data['UUID']])->first();

		if($map->is_null())
		{
			$post = Post::create([
				'topic_id' => $topic_id,
			]);

			$map = Map::create([
				'uuid' => $data['UUID'],
				'target_class_name' => $post->class_name(),
				'target_id' => $post->id(),
			]);
		}
		else
		{
			$post = $map->target();
		}

		$map->set('container_uuid', $data['TopicUUID']);
		$map->set('author_uuid', $data['Author']['UUID']);

		$data = [
			'topic_id'		=> $topic_id,
//			'number',
			'create_time'	=> strtotime(@$data['Date']),
//			'create_datetime' => 'time',
//			'owner_id' => 'user_id',
//			'type',
			'text' => $data['Text'],
//			'modify_time' => 'edit_time',
//			'last_editor_id' => 'edit_user_id',
//			'hide_time',
//			'hide_user_id',
//			'ip_address',
			'is_approved' => true,
//			'author_name' => $data['Author']['Title'],
		];

		$post->set_fields($data);

		$post->save();

		return $post;
	}


	static function find_or_create($data)
	{
		$infonesy_post = Adapter\Post::find(['infonesy_uuid' => $data['UUID']])->first();

		// If post found in adapter database
		if($infonesy_post->is_not_null())
			return self::loader($infonesy_post, NULL, $data);

		$author = User::find_or_create($data['Author']);
		$topic = Topic::find_or_create(['UUID' => $data['TopicUUID']]);

		$data['flarum_b2_topic'] = $topic;

		echo "tid={$topic->id()}, uid={$author->id()}, date={$data['Date']}\n";

		// Make flarum post
		$flarum_post = self::create([
			'topic_id'		=> $topic->id(),
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

	static function loader($infonesy_post, $b2_post, $data)
	{
		if(!$b2_post)
			$b2_post = B2Model\Post::load($infonesy_post->flarum_post_id());

		if(empty($data['flarum_b2_topic']))
			$topic = Topic::find_or_create(['UUID' => $data['TopicUUID']]);
		else
			$topic = $data['flarum_b2_topic'];

		if((!$b2_post->topic_id() || $b2_post->topic_id() == config('flarum.quarantine.topic_id')) && !empty($data['TopicUUID']))
			$b2_post->set_topic_id($topic->id());

		if(!empty($data['Date']))
		{
			$ts = strtotime($data['Date']);
//			$time = \Carbon\Carbon::createFromTimestampUTC($ts)->toDateTimeString();
//			echo "; ts=".date("r", $ts)."; dt=".(new \DateTime($time))."; ";
//			$b2_post->set_create_datetime(new \DateTime($time));
//			$b2_post->set_create_time($ts);
			// Forced UTC time for Flarum ugliness o_O
			$b2_post->set_create_datetime(gmdate('Y-m-d H:i:s', $ts));
		}

		$b2_post->save();

		$topic->recalculate();

		return $b2_post;
	}

	static function create($data)
	{
		$app = App::instance();
		$flarum_app = $app->flarum_app;
//		$flarum_actor = Sub\User::find(popval($data, 'author_id'));
		$flarum_actor = \Flarum\Core\User::find(popval($data, 'author_id'));
		$flarum_discussion = \Flarum\Core\Discussion::find(popval($data, 'topic_id'));

//		$flarum_actor->forced_time = true;

		$flarum_data = [
			'attributes' => [
				'content' => $data['text'],
//				'time' => \Carbon\Carbon::createFromTimestampUTC($data['create_time'])->toDateTimeString(),
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
