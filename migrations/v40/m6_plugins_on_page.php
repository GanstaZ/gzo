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

class m6_plugins_on_page extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	public static function depends_on(): array
	{
		return [gzo::MAIN_MIGRATION];
	}

	/**
	* Add the initial data in the database
	*/
	public function update_data(): array
	{
		return [
			['custom', [[$this, 'add_pages']]],
		];
	}

	/**
	* Custom function to add pages data
	*/
	public function add_pages(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . gzo::PLUGINS_ON_PAGE))
		{
			$pages_data = [
				[
					'name'	 => 'ganstaz_mini_profile',
					'page'	 => 'app',
					'active' => 1,
				],
				[
					'name'	 => 'ganstaz_information',
					'page'	 => 'app',
					'active' => 1,
				],
				[
					'name'	 => 'ganstaz_the_team',
					'page'	 => 'app',
					'active' => 1,
				],
				[
					'name'	 => 'ganstaz_top_posters',
					'page'	 => 'app',
					'active' => 1,
				],
				[
					'name'	 => 'ganstaz_recent_posts',
					'page'	 => 'app',
					'active' => 1,
				],
				[
					'name'	 => 'ganstaz_recent_topics',
					'page'	 => 'app',
					'active' => 0,
				],
				[
					'name'	 => 'ganstaz_whos_online',
					'page'	 => 'app',
					'active' => 1,
				],
				[
					'name'	 => 'ganstaz_announcement',
					'page'	 => 'app',
					'active' => 1,
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . gzo::PLUGINS_ON_PAGE);

			foreach ($pages_data as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
