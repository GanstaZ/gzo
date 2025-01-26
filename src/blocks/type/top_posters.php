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
* Top Posters block
*/
class top_posters extends base
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
		$sql = 'SELECT user_id, user_posts, username, user_colour, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height
				FROM ' . USERS_TABLE . '
				WHERE user_id <> ' . (int) ANONYMOUS . '
					AND user_type <> ' . (int) USER_IGNORE . '
					AND user_posts > 0
				ORDER BY user_posts DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['gzo_user_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$data = [
				'user_avatar'        => $row['user_avatar'],
				'user_avatar_type'   => $row['user_avatar_type'],
				'user_avatar_width'  => $row['user_avatar_width'],
				'user_avatar_height' => $row['user_avatar_height'],
			];

			$this->twig->assign_block_vars('top_posters', [
				'avatar' => [(array) $data],
				'posts'  => (int) $row['user_posts'],
				'poster' => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
