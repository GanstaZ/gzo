<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\migrations\v40;

use ganstaz\gzo\src\enum\gzo;

class m4_plugins_on_page extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc}
	 */
	public static function depends_on(): array
	{
		return [gzo::MAIN_MIGRATION];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data(): array
	{
		return [
			['custom', [[$this, 'add_plugins_page_data']]],
		];
	}

	public function add_plugins_page_data(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . gzo::PLUGINS_ON_PAGE))
		{
			$on_page = [
				[
					'name'      => 'ganstaz_mini_profile',
					'page_name' => 'app',
					'active'    => 1,
				],
				[
					'name'      => 'ganstaz_information',
					'page_name' => 'app',
					'active'    => 1,
				],
				[
					'name'      => 'ganstaz_the_team',
					'page_name' => 'app',
					'active'    => 1,
				],
				[
					'name'      => 'ganstaz_top_posters',
					'page_name' => 'app',
					'active'    => 1,
				],
				[
					'name'      => 'ganstaz_recent_posts',
					'page_name' => 'app',
					'active'    => 1,
				],
				[
					'name'      => 'ganstaz_recent_topics',
					'page_name' => 'app',
					'active'    => 0,
				],
				[
					'name'      => 'ganstaz_whos_online',
					'page_name' => 'app',
					'active'    => 1,
				],
				[
					'name'      => 'ganstaz_announcement',
					'page_name' => 'app',
					'active'    => 1,
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . gzo::PLUGINS_ON_PAGE);

			foreach ($on_page as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
