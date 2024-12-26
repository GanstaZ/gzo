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

class m2_plugins extends \phpbb\db\migration\migration
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
	* Custom function to add pages data
	*/
	public function add_plugins(): void
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . gzo::PLUGINS))
		{
			$plugins = [
				[
					'title'	=> 'ACP_GZO_MAIN',
					'auth'	=> '',
					'cat'	=> 'GZO_DASHBOARD',
					'type'  => 'gzo',
					'route' => 'gzo_main',
					'icon'	=> 'home',
				],
				[
					'title'	=> 'ACP_GZO_GLOBAL',
					'auth'	=> '',
					'cat'	=> 'ACP_GZO_TITLE',
					'type'  => 'gzo',
					'route' => 'gzo_settings',
					'icon'	=> 'globe',
				],
				[
					'title'	=> 'ACP_GZO_BLOCKS',
					'auth'	=> '',
					'cat'	=> 'ACP_GZO_TITLE',
					'type'  => 'gzo',
					'route' => 'gzo_blocks',
					'icon'	=> 'cube',
				],
				[
					'title'	=> 'ACP_GZO_PAGE',
					'auth'	=> '',
					'cat'	=> 'ACP_GZO_TITLE',
					'type'  => 'gzo',
					'route' => 'gzo_pages',
					'icon'	=> 'cog',
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
