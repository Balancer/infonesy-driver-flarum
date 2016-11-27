<?php

namespace Infonesy\Driver\Flarum;

class Post extends \B2\Flarum\Post
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
				'author_id' => \B2\Cfg::get('infonesy.guest.user_id'),
				'text' => $data['Text'],
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
			'text' => $data['Text'],
//			'number',
			'create_time'	=> strtotime(@$data['Date']),
//			'create_datetime' => 'time',
//			'author_id' => 'user_id',
//			'type',
//			'text' => $data['Text'],
//			'modify_time' => 'edit_time',
//			'last_editor_id' => 'edit_user_id',
//			'hide_time',
//			'hide_user_id',
//			'ip_address',
			'is_approved' => true,
//			'author_name' => $data['Author']['Title'],
		];

//		$post->set_fields($data);

		$post->edit($data);

		$post->set_is_approved(true);

		$post->save();

		return $post;
	}
}
