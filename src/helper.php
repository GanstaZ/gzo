<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src;

use phpbb\db\driver\driver_interface;

/**
* Helper class
*/
class helper
{
	public function __construct(private driver_interface $db)
	{
	}

	/**
	* Get options as forum_ids
	*/
	public function get_forum_ids(): array
	{
		$sql = 'SELECT forum_id
				FROM ' . FORUMS_TABLE . '
				WHERE forum_type = ' . FORUM_POST . '
					AND news_fid_enable = 1';
		$result = $this->db->sql_query($sql, 3600);

		$forum_ids = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$forum_ids[] = (int) $row['forum_id'];
		}
		$this->db->sql_freeresult($result);

		return $forum_ids ?? [];
	}
}
