<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core;

use phpbb\db\driver\driver_interface;

/**
* GZ Web: helper class
*/
class helper
{
	/** @var driver_interface */
	protected $db;

	/**
	* Constructor
	*
	* @param driver_interface $db Database object
	*/
	public function __construct(driver_interface $db)
	{
		$this->db = $db;
	}

	/**
	* Get options as forum_ids
	*
	* @return array
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

	/**
	* Get username
	*
	* @param int $user_id
	* @return array
	*/
	public function get_user_name(int $user_id)
	{
		$sql = 'SELECT username
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row['username'];
	}

	/**
	* Truncate title
	*
	* @param string		 $title	 Truncate title
	* @param int		 $length Max length of the string
	* @param null|string $ellips Language ellips
	* @return string
	*/
	public function truncate(string $title, int $length, $ellips = null): string
	{
		return truncate_string(censor_text($title), $length, 255, false, $ellips ?? '...');
	}
}
