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
use phpbb\template\template;

/**
* DLS Web Top Posters block
*/
class top_posters implements block_interface
{
	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var template */
	protected $template;

	/**
	* Constructor
	*
	* @param config			  $config	Config object
	* @param driver_interface $db		Database object
	* @param template		  $template Template object
	*/
	public function __construct(config $config, driver_interface $db, template $template)
	{
		$this->config = $config;
		$this->db = $db;
		$this->template = $template;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data(): array
	{
		return [
			'block_name' => 'dls_top_posters',
			'cat_name' => 'side',
			'ext_name' => 'dls_web',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
		$sql = 'SELECT user_id, user_type, user_posts, username, user_colour
				FROM ' . USERS_TABLE . '
				WHERE user_id <> ' . (int) ANONYMOUS . '
					AND user_type <> ' . (int) USER_IGNORE . '
					AND user_posts > 0
				ORDER BY user_posts DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('top_posters', [
				'top' => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
				'posts' => (int) $row['user_posts'],
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
