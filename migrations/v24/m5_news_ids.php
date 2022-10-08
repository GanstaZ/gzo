<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\migrations\v24;

class m5_news_ids extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'forums', 'news_fid_enable');
	}

	/**
	* {@inheritdoc}
	*/
	static public function depends_on()
	{
		return ['\ganstaz\web\migrations\v24\m1_main'];
	}

	/**
	* Add the table schemas to the database:
	*
	* @return array Array of table schema
	* @access public
	*/
	public function update_schema()
	{
		return [
			'add_columns' => [
				$this->table_prefix . 'forums' => [
					'news_fid_enable' => ['BOOL', 0],
				],
			],
		];
	}

	/**
	* Drop the schemas from the database
	*
	* @return array Array of table schema
	* @access public
	*/
	public function revert_schema()
	{
		return [
			'drop_columns' => [
				$this->table_prefix . 'forums' => [
					'news_fid_enable',
				],
			],
		];
	}
}
