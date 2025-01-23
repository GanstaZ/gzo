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

class m2_area extends \phpbb\db\migration\migration
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
			['custom', [[$this, 'add_area_data']]],
		];
	}

	/**
	* Custom function to add area data
	*/
	public function add_area_data(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . gzo::AREA))
		{
			$items = [
				[
					'cat'	=> 'GZO_QUICK_ACCESS',
					'title'	=> 'GZO_MAIN_PAGE',
					'type'  => 'gzo',
					'auth'	=> '',
					'route' => 'gzo_main',
					'icon'	=> '',
				],
				[
					'cat'	=> 'GZO_GLOBAL',
					'title'	=> 'GZO_SETTINGS',
					'type'  => 'gzo',
					'auth'	=> '',
					'route' => 'gzo_settings',
					'icon'	=> '',
				],
				[
					'cat'	=> 'GZO_GLOBAL',
					'title'	=> 'GZO_BLOCKS',
					'type'  => 'gzo',
					'auth'	=> '',
					'route' => 'gzo_blocks',
					'icon'	=> '',
				],
				[
					'cat'	=> 'GZO_GLOBAL',
					'title'	=> 'GZO_PAGES',
					'type'  => 'gzo',
					'auth'	=> '',
					'route' => 'gzo_pages',
					'icon'	=> '',
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . gzo::AREA);

			foreach ($items as $item)
			{
				$insert_buffer->insert($item);
			}

			$insert_buffer->flush();
		}
	}
}
