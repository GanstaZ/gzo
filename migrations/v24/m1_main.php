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

class m1_main extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	public function effectively_installed(): bool
	{
		return $this->check('blocks') && $this->check('pages');
	}

	/**
	* Check condition exists for a given table name
	*/
	public function check(string $name): bool
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'gzo_' . $name);
	}

	/**
	* {@inheritdoc}
	*/
	public static function depends_on(): array
	{
		return ['\phpbb\db\migration\data\v33x\v3310'];
	}

	/**
	* Add the table schemas to the database:
	*/
	public function update_schema(): array
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'gzo_blocks' => [
					'COLUMNS' => [
						'id'	   => ['UINT', null, 'auto_increment'],
						'name'	   => ['VCHAR', ''],
						'ext_name' => ['VCHAR', ''],
						'position' => ['UINT', 0],
						'active'   => ['BOOL', 0],
						'section'  => ['VCHAR', ''],
					],
					'PRIMARY_KEY' => ['id'],
				],
				$this->table_prefix . 'gzo_pages' => [
					'COLUMNS' => [
						'id'		 => ['UINT', null, 'auto_increment'],
						'name'		 => ['VCHAR', ''],
						'active'	 => ['BOOL', 0],
						'allow'		 => ['BOOL', 0],
						'changeable' => ['BOOL', 0],
						'gzo_right'	 => ['BOOL', 0],
						'gzo_left'	 => ['BOOL', 0],
						'gzo_middle' => ['BOOL', 0],
						'gzo_top'	 => ['BOOL', 0],
						'gzo_bottom' => ['BOOL', 0],
					],
					'PRIMARY_KEY' => ['id'],
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
			'drop_tables' => [
				$this->table_prefix . 'gzo_blocks',
				$this->table_prefix . 'gzo_pages',
			],
		];
	}
}
