<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
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
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'gz_pages'))
		{
			$pages_data = [
				[
					'name'		 => 'app',
					'active'	 => 1,
					'allow'		 => 0,
					'changeable' => 1,
					'gz_special' => 1,
					'gz_right'	 => 1,
					'gz_left'	 => 0,
					'gz_middle'  => 1,
					'gz_top'	 => 0,
					'gz_bottom'  => 0,
				],
				[
					'name'		 => 'news',
					'active'	 => 1,
					'allow'		 => 0,
					'changeable' => 0,
					'gz_special' => 1,
					'gz_right'	 => 1,
					'gz_left'	 => 0,
					'gz_middle'  => 1,
					'gz_top'	 => 0,
					'gz_bottom'  => 0,
				],
				[
					'name'		 => 'article',
					'active'	 => 1,
					'allow'		 => 0,
					'changeable' => 0,
					'gz_special' => 1,
					'gz_right'	 => 1,
					'gz_left'	 => 0,
					'gz_middle'  => 1,
					'gz_top'	 => 0,
					'gz_bottom'  => 0,
				],
				[
					'name'		 => 'index',
					'active'	 => 1,
					'allow'		 => 0,
					'changeable' => 0,
					'gz_special' => 0,
					'gz_right'	 => 1,
					'gz_left'	 => 0,
					'gz_middle'  => 0,
					'gz_top'	 => 0,
					'gz_bottom'  => 0,
				],
				[
					'name'		 => 'memberlist',
					'active'	 => 0,
					'allow'		 => 0,
					'changeable' => 0,
					'gz_special' => 0,
					'gz_right'	 => 0,
					'gz_left'	 => 0,
					'gz_middle'  => 0,
					'gz_top'	 => 0,
					'gz_bottom'  => 0,
				],
				[
					'name'		 => 'viewforum',
					'active'	 => 0,
					'allow'		 => 0,
					'changeable' => 0,
					'gz_special' => 0,
					'gz_right'	 => 0,
					'gz_left'	 => 0,
					'gz_middle'  => 0,
					'gz_top'	 => 0,
					'gz_bottom'  => 0,
				],
				[
					'name'		 => 'viewtopic',
					'active'	 => 0,
					'allow'		 => 0,
					'changeable' => 0,
					'gz_special' => 0,
					'gz_right'	 => 0,
					'gz_left'	 => 0,
					'gz_middle'  => 0,
					'gz_top'	 => 0,
					'gz_bottom'  => 0,
				],
				[
					'name'		 => 'search',
					'active'	 => 0,
					'allow'		 => 0,
					'changeable' => 0,
					'gz_special' => 0,
					'gz_right'	 => 0,
					'gz_left'	 => 0,
					'gz_middle'  => 0,
					'gz_top'	 => 0,
					'gz_bottom'  => 0,
				],
				[
					'name'		 => 'faq',
					'active'	 => 0,
					'allow'		 => 0,
					'changeable' => 0,
					'gz_special' => 0,
					'gz_right'	 => 0,
					'gz_left'	 => 0,
					'gz_middle'  => 0,
					'gz_top'	 => 0,
					'gz_bottom'  => 0,
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . 'gz_pages');

			foreach ($pages_data as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
