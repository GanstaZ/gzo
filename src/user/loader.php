<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\user;

use phpbb\db\driver\driver_interface;

/**
* Users loader - Borrowed from phpBB users_loader class
*/
final class loader
{
	public array $users = [];

	public function __construct(
		protected driver_interface $db,
		protected readonly string $root_path,
		protected readonly string $php_ext,
		public readonly string $users_table
	)
	{
	}

	/**
	* @param int $user_id
	*/
	public function load_by_id(int $user_id): void
	{
		$sql = 'SELECT *
				FROM ' . $this->users_table . '
				WHERE user_id = ' . $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row)
		{
			$this->users[$row['user_id']] = $row;
		}
	}

	public function load_user(array $data): void
	{
		if (!isset($this->users[$data['user_id']]))
		{
			$this->users[$data['user_id']] = $data;
		}
	}

	public function get_user(int $user_id): array
	{
		if (isset($this->users[$user_id]))
		{
			return $this->users[$user_id];
		}

		$this->load_by_id($user_id);

		return $this->get_user($user_id);
	}

	public function get_username(int $user_id): string
	{
		if (isset($this->users[$user_id]))
		{
			return $this->users[$user_id]['username'];
		}

		$sql = 'SELECT username
				FROM ' . $this->users_table . '
				WHERE user_id = ' . $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row['username'] ?? '';
	}

	public function get_username_data(int $user_id): array
	{
		if (!($user = $this->get_user($user_id)))
		{
			return [];
		}

		return [
			'id'	=> (int) $user['user_id'],
			'name'	=> $user['username'],
			'color' => $user['user_colour'],
		];
	}

	public function get_avatar_data(int $user_id): array
	{
		if (!($user = $this->get_user($user_id)))
		{
			return [];
		}

		return [
			'user_avatar'		 => $user['user_avatar'],
			'user_avatar_type'	 => $user['user_avatar_type'],
			'user_avatar_width'	 => $user['user_avatar_width'],
			'user_avatar_height' => $user['user_avatar_height'],
		];
	}

	public function get_rank_data(array $data): array
	{
		if (!function_exists('phpbb_get_user_rank'))
		{
			include("{$this->root_path}includes/functions_display.{$this->php_ext}");
		}

		$rank = phpbb_get_user_rank($data, ($data['user_id'] == ANONYMOUS ? false : $data['user_posts']));

		return [
			'rank_title'   => $rank['title'],
			'rank_img'	   => $rank['img'],
			'rank_img_src' => $rank['img_src']
		];
	}
}
