<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\migrations\v24;

class m3_blocks extends \phpbb\db\migration\migration
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
			['custom', [[$this, 'add_blocks']]],
		];
	}

	/**
	* Custom function to add blocks data
	*/
	public function add_blocks()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'gz_blocks'))
		{
			$blocks_data = [
				[
					'name'	   => 'ganstaz_news',
					'ext_name' => 'ganstaz_web',
					'position' => 1,
					'active'   => 1,
					'section'  => 'gz_special',
				],
				[
					'name'	   => 'ganstaz_mini_profile',
					'ext_name' => 'ganstaz_web',
					'position' => 1,
					'active'   => 1,
					'section'  => 'gz_right',
				],
				[
					'name'	   => 'ganstaz_information',
					'ext_name' => 'ganstaz_web',
					'position' => 2,
					'active'   => 1,
					'section'  => 'gz_right',
				],
				[
					'name'	   => 'ganstaz_the_team',
					'ext_name' => 'ganstaz_web',
					'position' => 3,
					'active'   => 1,
					'section'  => 'gz_right',
				],
				[
					'name'	   => 'ganstaz_top_posters',
					'ext_name' => 'ganstaz_web',
					'position' => 4,
					'active'   => 1,
					'section'  => 'gz_right',
				],
				[
					'name'	   => 'ganstaz_recent_posts',
					'ext_name' => 'ganstaz_web',
					'position' => 5,
					'active'   => 1,
					'section'  => 'gz_right',
				],
				[
					'name'	   => 'ganstaz_recent_topics',
					'ext_name' => 'ganstaz_web',
					'position' => 6,
					'active'   => 0,
					'section'  => 'gz_left',
				],
				[
					'name'	   => 'ganstaz_whos_online',
					'ext_name' => 'ganstaz_web',
					'position' => 1,
					'active'   => 1,
					'section'  => 'gz_middle',
				],
			];

			$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . 'gz_blocks');

			foreach ($blocks_data as $row)
			{
				$insert_buffer->insert($row);
			}

			$insert_buffer->flush();
		}
	}
}
