<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks\block;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use dls\web\core\helper;

/**
* DLS Web The Team block
*/
class the_team implements block_interface
{
	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var helper */
	protected $helper;

	/**
	* Constructor
	*
	* @param config			  $config Config object
	* @param driver_interface $db	  Database object
	* @param helper			  $helper dls helper object
	*/
	public function __construct(config $config, driver_interface $db, helper $helper)
	{
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data(): array
	{
		return [
			'block_name' => 'dls_the_team',
			'cat_name' => 'right',
			'ext_name' => 'dls_web',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
		$group_id = (int) $this->config['dls_the_team_fid'];

		$sql = 'SELECT group_name, group_type
				FROM ' . GROUPS_TABLE . '
				WHERE group_id = ' . $group_id;
		$result = $this->db->sql_query($sql, 3600);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$this->helper->assign('var', 'get_team_name', $this->helper->get_name($row['group_name']));

		$sql = 'SELECT ug.*, u.username, u.user_id, u.user_colour, u.username_clean
				FROM ' . USER_GROUP_TABLE . ' ug, ' . USERS_TABLE . ' u
				WHERE ug.user_id = u.user_id
					AND ug.user_pending = 0
					AND ug.group_id = ' . $group_id;
		$result = $this->db->sql_query($sql, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->helper->assign('block_vars', 'the_team', [
				'member' => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
