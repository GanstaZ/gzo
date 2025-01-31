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

class m3_plugins extends \phpbb\db\migration\migration
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
			['custom', [[$this, 'add_plugins']]],
		];
	}

	/**
	* Custom function to add plugins
	*/
	public function add_plugins(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . gzo::PLUGINS))
		{
			$plugins = [
				[
					'name'	   => 'ganstaz_announcement',
					'type'	   => 'block',
					'ext_name' => 'ganstaz_gzo',
					'position' => 1,
					'active'   => 1,
					'section'  => 'gzo_announcement',
				],
				[
					'name'	   => 'ganstaz_mini_profile',
					'type'	   => 'block',
					'ext_name' => 'ganstaz_gzo',
					'position' => 1,
					'active'   => 1,
					'section'  => 'gzo_side',
				],
				[
					'name'	   => 'ganstaz_information',
					'type'	   => 'block',
					'ext_name' => 'ganstaz_gzo',
					'position' => 2,
					'active'   => 1,
					'section'  => 'gzo_side',
				],
				[
					'name'	   => 'ganstaz_the_team',
					'type'	   => 'block',
					'ext_name' => 'ganstaz_gzo',
					'position' => 3,
					'active'   => 1,
					'section'  => 'gzo_side',
				],
				[
					'name'	   => 'ganstaz_top_posters',
					'type'	   => 'block',
					'ext_name' => 'ganstaz_gzo',
					'position' => 4,
					'active'   => 1,
					'section'  => 'gzo_side',
				],
				[
					'name'	   => 'ganstaz_recent_posts',
					'type'	   => 'block',
					'ext_name' => 'ganstaz_gzo',
					'position' => 5,
					'active'   => 1,
					'section'  => 'gzo_side',
				],
				[
					'name'	   => 'ganstaz_recent_topics',
					'type'	   => 'block',
					'ext_name' => 'ganstaz_gzo',
					'position' => 6,
					'active'   => 0,
					'section'  => 'gzo_side',
				],
				[
					'name'	   => 'ganstaz_whos_online',
					'type'	   => 'block',
					'ext_name' => 'ganstaz_gzo',
					'position' => 1,
					'active'   => 1,
					'section'  => 'gzo_online',
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . gzo::PLUGINS);

			foreach ($plugins as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
