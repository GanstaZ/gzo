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

class m6_pages extends \phpbb\db\migration\migration
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
		if ($this->db_tools->sql_table_exists($this->table_prefix . gzo::PAGES))
		{
			$pages_data = [
				[
					'name'		 => 'app',
					'active'	 => 1,
					'allow'		 => 0,
					'gzo_right'	 => 1,
					'gzo_left'	 => 0,
					'gzo_middle' => 1,
				],
				[
					'name'		 => 'news',
					'active'	 => 1,
					'allow'		 => 0,
					'gzo_right'	 => 1,
					'gzo_left'	 => 0,
					'gzo_middle' => 1,
				],
				[
					'name'		 => 'article',
					'active'	 => 1,
					'allow'		 => 0,
					'gzo_right'	 => 1,
					'gzo_left'	 => 0,
					'gzo_middle' => 1,
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . gzo::PAGES);

			foreach ($pages_data as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
