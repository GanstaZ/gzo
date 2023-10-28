<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\migrations\v24;

use ganstaz\gzo\src\enum\gzo;

class m3_blocks extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	public static function depends_on()
	{
		return [gzo::MAIN_MIGRATION];
	}

	/**
	* Add the initial data in the database
	*/
	public function update_data(): array
	{
		return [
			['custom', [[$this, 'add_blocks']]],
		];
	}

	/**
	* Custom function to add blocks data
	*/
	public function add_blocks(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'gzo_blocks'))
		{
			$blocks_data = [
				[
					'name'	   => 'ganstaz_mini_profile',
					'ext_name' => 'ganstaz_gzo',
					'position' => 1,
					'active'   => 1,
					'section'  => 'gzo_right',
				],
				[
					'name'	   => 'ganstaz_information',
					'ext_name' => 'ganstaz_gzo',
					'position' => 2,
					'active'   => 1,
					'section'  => 'gzo_right',
				],
				[
					'name'	   => 'ganstaz_the_team',
					'ext_name' => 'ganstaz_gzo',
					'position' => 3,
					'active'   => 1,
					'section'  => 'gzo_right',
				],
				[
					'name'	   => 'ganstaz_top_posters',
					'ext_name' => 'ganstaz_gzo',
					'position' => 4,
					'active'   => 1,
					'section'  => 'gzo_right',
				],
				[
					'name'	   => 'ganstaz_recent_posts',
					'ext_name' => 'ganstaz_gzo',
					'position' => 5,
					'active'   => 1,
					'section'  => 'gzo_right',
				],
				[
					'name'	   => 'ganstaz_recent_topics',
					'ext_name' => 'ganstaz_gzo',
					'position' => 1,
					'active'   => 0,
					'section'  => 'gzo_left',
				],
				[
					'name'	   => 'ganstaz_whos_online',
					'ext_name' => 'ganstaz_gzo',
					'position' => 1,
					'active'   => 1,
					'section'  => 'gzo_middle',
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . 'gzo_blocks');

			foreach ($blocks_data as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
