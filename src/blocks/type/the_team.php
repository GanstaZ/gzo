<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\blocks\type;

/**
* The Team block
*/
class the_team extends base
{
	/**
	* {@inheritdoc}
	*/
	public function get_block_data(): array
	{
		return [
			'section'  => 'gzo_right',
			'ext_name' => 'ganstaz_gzo',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
		// Fast fix.. Needs a better solution later.
		$team = (int) $this->config['gzo_the_team_fid'];
		$default = 5;

		$sql = 'SELECT group_name, group_type
				FROM ' . GROUPS_TABLE . '
				WHERE group_id = ' . $team;
		$result = $this->db->sql_query($sql, 3600);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$this->twig->assign_var('team_name', $row['group_name']);

		if (!function_exists('phpbb_get_user_rank'))
		{
			include("{$this->root_path}includes/functions_display.{$this->php_ext}");
		}

		$sql = 'SELECT u.username, u.user_id, u.user_colour, u.username_clean, u.user_rank, u.user_posts, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
				FROM ' . USER_GROUP_TABLE . ' ug, ' . USERS_TABLE . ' u
				WHERE ug.user_id = u.user_id
					AND ug.user_pending = 0
					AND ug.group_id = ' . $team;
		$result = $this->db->sql_query($sql, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$data = [
				'user_avatar'        => $row['user_avatar'],
				'user_avatar_type'   => $row['user_avatar_type'],
				'user_avatar_width'  => $row['user_avatar_width'],
				'user_avatar_height' => $row['user_avatar_height'],
			];

			$rank = phpbb_get_user_rank(['user_rank' => $row['user_rank']], $row['user_posts']);

			$this->twig->assign_block_vars('the_team', [
				'name'   => (string) get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
				'avatar' => [(array) $data],
				'rank'   => (string) $rank['title']
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
