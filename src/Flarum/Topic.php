<?php

namespace Infonesy\Driver\Flarum;

class Topic extends \B2\Obj
{
	static function find_or_create($data)
	{
		$infonesy_topic = Adapter\Topic::find(['infonesy_uuid' => $data['UUID']])->first();

		// If topic found in adapter database
		if($infonesy_topic->is_not_null())
			return self::loader($infonesy_topic, NULL, $data);

		if(!empty($data['Author']))
		{
			$author = User::find_or_create($data['Author']);
			$author_id = $author->id();
		}
		else
			$author_id = 0;

		// Make flarum topic
		$flarum_topic = B2Model\Topic::create([
			'title' => empty($data['Title']) ? 'Карантин' : $data['Title'],
			'comments_count' => 1,
//			'participants_count',
//			'number_index',
			'create_time' => empty($data['Date']) ? NULL : strtotime($data['Date']),
			'author_id' => $author_id,
//			'start_post_id',
//			'last_time',
//			'last_user_id',
//			'last_post_id',
//			'last_post_number',
//			'hide_time',
//			'hide_user_id',
//			'slug',
//			'is_approved',
//			'is_locked',
//			'is_sticky',
		]);

		// Make adapter link
		$infonesy_topic = Adapter\Topic::create([
			'flarum_topic_id' => $flarum_topic->id(),
			'infonesy_uuid' => $data['UUID'],
		]);

		return self::loader($infonesy_topic, $flarum_topic, $data);
	}

	static function loader($infonesy_topic, $b2_topic, $data)
	{
		if(!$b2_topic)
			$b2_topic = B2Model\Topic::load($infonesy_topic->flarum_topic_id());

		if($b2_topic->title() == 'Карантин' && !empty($data['Title']))
		{
			echo "{$b2_topic->id()}: Карантин -> {$data['Title']}\n";
			$b2_topic->set_title($data['Title']);
		}

		$b2_topic->save();

		return $b2_topic;
	}
}
