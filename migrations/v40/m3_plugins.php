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
	 * {@inheritdoc}
	 */
	public function update_data(): array
	{
		return [
			['custom', [[$this, 'add_plugins_data']]],
		];
	}

	public function add_plugins_data(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . gzo::PLUGINS))
		{
			$plugins = [
				[
					'name'	   => gzo::PLUGIN['profile'],
					'ext_name' => gzo::EXT_NAME,
					'position' => 1,
					'section'  => gzo::SECTION['side'],
				],
				[
					'name'	   => gzo::PLUGIN['info'],
					'ext_name' => gzo::EXT_NAME,
					'position' => 2,
					'section'  => gzo::SECTION['side'],
				],
				[
					'name'	   => gzo::PLUGIN['group'],
					'ext_name' => gzo::EXT_NAME,
					'position' => 3,
					'section'  => gzo::SECTION['side'],
				],
				[
					'name'	   => gzo::PLUGIN['poster'],
					'ext_name' => gzo::EXT_NAME,
					'position' => 4,
					'section'  => gzo::SECTION['side'],
				],
				[
					'name'	   => gzo::PLUGIN['posts'],
					'ext_name' => gzo::EXT_NAME,
					'position' => 5,
					'section'  => gzo::SECTION['side'],
				],
				[
					'name'	   => gzo::PLUGIN['topics'],
					'ext_name' => gzo::EXT_NAME,
					'position' => 6,
					'section'  => gzo::SECTION['side'],
				],
				[
					'name'	   => gzo::PLUGIN['online'],
					'ext_name' => gzo::EXT_NAME,
					'position' => 1,
					'section'  => gzo::SECTION['bottom'],
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
