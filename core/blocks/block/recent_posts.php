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
* DLS Web Recent Posts block
*/
class recent_posts implements block_interface
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
			'block_name' => 'dls_recent_posts',
			'cat_name' => 'right',
			'ext_name' => 'dls_web',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
		$sql = 'SELECT p.post_id, t.topic_id, t.topic_visibility, t.topic_title, t.topic_time, t.topic_status, t.topic_last_post_id
				FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
				WHERE t.topic_last_post_id = p.post_id
					AND t.topic_status <> ' . ITEM_MOVED . '
					AND t.topic_visibility = 1
				ORDER BY p.post_id DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_user_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->helper->assign('block_vars', 'recent_posts', [
				'link'	=> append_sid("{$this->helper->get('root_path')}viewtopic.{$this->helper->get('php_ext')}", "t={$row['topic_id']}#p{$row['post_id']}"),
				'title' => $this->helper->truncate($row['topic_title'], $this->config['dls_title_length']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
