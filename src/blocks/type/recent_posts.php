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
* Recent Posts block
*/
class recent_posts extends base
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
		$sql = 'SELECT p.post_id, t.topic_id, t.topic_title
				FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
				WHERE t.topic_last_post_id = p.post_id
					AND t.topic_status <> ' . ITEM_MOVED . '
					AND t.topic_visibility = 1
				ORDER BY p.post_id DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['gzo_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->twig->assign_block_vars('recent_posts', [
				'link'	=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "t={$row['topic_id']}#p{$row['post_id']}"),
				'title' => $this->helper->truncate($row['topic_title'], $this->config['gzo_title_length']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
