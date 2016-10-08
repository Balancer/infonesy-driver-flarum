<?php

namespace Infonesy\Driver\Flarum;

class User extends \B2\Obj
{
	static function find_or_create($data)
	{
		$infonesy_user = Adapter\User::find(['email_md5' => $data['EmailMD5']])->first();

		// If user found in adapter database
		if($infonesy_user->is_not_null())
			return self::loader($infonesy_user, NULL, $data);

		foreach(B2Model\User::find()->all() as $flarum_user)
		{
			$email_md5 = md5($flarum_user->email());
			$infonesy_user = Adapter\User::find(['email_md5' => $email_md5])->first();
			if($infonesy_user->is_null())
			{
				// Make adapter record
				$infonesy_user = Adapter\User::create([
					'flarum_user_id' => $flarum_user->id(),
					'email_md5' => $email_md5,
				]);
			}

			// If user found in adapter database by hash
			if($infonesy_user->email_md5() == $data['EmailMD5'])
				return self::loader($infonesy_user, $flarum_user, $data);
		}

		$idx = 0;

		while(true)
		{
			if($idx++)
				$test_name = $data['Title'].'_'.$idx;
			else
				$test_name = $data['Title'];

			$duplicate_user_check = B2Model\User::find(['title' => $test_name])->first();
			if($duplicate_user_check->is_null())
			{
				$data['Title'] = $test_name;
				break;
			}
		}

		// Make flarum user
		$flarum_user = B2Model\User::create([
			'title'			=> $data['Title'],
			'email'			=> $data['EmailMD5'],
			'is_activated'	=> true,
			'password'		=> md5(rand()),
//			'bio',
//			'avatar_path',
//			'preferences',
//			'create_time'	=> strtotime($data['RegisterDate']),
//			'last_visit_time'=> strtotime($data['LastVisit']),
//			'read_time',
//			'notifications_read_time',
//			'discussions_count',
//			'comments_count',
//			'flags_read_time',
//			'suspend_until',
//			'twitter_id',
		]);

		// Make adapter link
		$infonesy_user = Adapter\User::create([
			'flarum_user_id' => $flarum_user->id(),
			'email_md5' => $data['EmailMD5'],
		]);

		return self::loader($infonesy_user, $flarum_user, $data);
	}

	static function loader($infonesy_user, $flarum_user, $data)
	{
		if(!$flarum_user)
			$flarum_user = B2Model\User::load($infonesy_user->flarum_user_id());

		if(!$flarum_user->create_time() && !empty($data['RegisterDate']))
			$flarum_user->set_create_time(strtotime($data['RegisterDate']));

		if(!$flarum_user->get('last_visit_time') && !empty($data['LastVisit']))
			$flarum_user->set_last_visit_time(strtotime($data['LastVisit']));

		if(!$flarum_user->email())
			$flarum_user->set_email(strtotime($data['EmailMD5']));

		if(!$infonesy_user->get('infonesy_uuid'))
			$infonesy_user->set_infonesy_uuid($data['UUID']);

		return $flarum_user;
	}
}
