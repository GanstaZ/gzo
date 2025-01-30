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

class m5_news_ids extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	public function effectively_installed(): bool
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'forums', 'news_fid_enable');
	}

	/**
	* {@inheritdoc}
	*/
	public static function depends_on(): array
	{
		return [gzo::MAIN_MIGRATION];
	}

	/**
	* Add the table schemas to the database:
	*/
	public function update_schema(): array
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
	*/
	public function revert_schema(): array
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
