<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\migrations\v24;

class m6_pages extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	static public function depends_on()
	{
		return ['\ganstaz\web\migrations\v24\m1_main'];
	}

	/**
	* Add the initial data in the database
	*
	* @return array Array of table data
	* @access public
	*/
	public function update_data()
	{
		return [
			['custom', [[$this, 'add_pages']]],
		];
	}

	/**
	* Custom function to add pages data
	*/
	public function add_pages()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'gzo_pages'))
		{
			$pages_data = [
				[
					'name'		 => 'app',
					'active'	 => 1,
					'allow'		 => 0,
					'changeable' => 1,
					'gzo_special' => 1,
					'gzo_right'	 => 1,
					'gzo_left'	 => 0,
					'gzo_middle' => 1,
					'gzo_top'	 => 0,
					'gzo_bottom' => 0,
				],
				[
					'name'		 => 'news',
					'active'	 => 1,
					'allow'		 => 0,
					'changeable' => 0,
					'gzo_special' => 1,
					'gzo_right'	 => 1,
					'gzo_left'	 => 0,
					'gzo_middle' => 1,
					'gzo_top'	 => 0,
					'gzo_bottom' => 0,
				],
				[
					'name'		 => 'article',
					'active'	 => 1,
					'allow'		 => 0,
					'changeable' => 0,
					'gzo_special' => 1,
					'gzo_right'	 => 1,
					'gzo_left'	 => 0,
					'gzo_middle' => 1,
					'gzo_top'	 => 0,
					'gzo_bottom' => 0,
				],
				[
					'name'		 => 'faq',
					'active'	 => 0,
					'allow'		 => 0,
					'changeable' => 0,
					'gzo_special' => 0,
					'gzo_right'	 => 0,
					'gzo_left'	 => 0,
					'gzo_middle' => 0,
					'gzo_top'	 => 0,
					'gzo_bottom' => 0,
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . 'gzo_pages');

			foreach ($pages_data as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
