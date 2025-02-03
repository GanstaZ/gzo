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

class m1_main extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed(): bool
	{
		return $this->check(gzo::AREA) && $this->check(gzo::PLUGINS) && $this->check(gzo::PLUGINS_ON_PAGE);
	}

	/**
	 * Check if given table exists or not
	 */
	public function check(string $name): bool
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . $name);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on(): array
	{
		return ['\phpbb\db\migration\data\v400\dev'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_schema(): array
	{
		return [
			'add_tables' => [
				$this->table_prefix . gzo::AREA => [
					'COLUMNS' => [
						'id'	 => ['UINT', null, 'auto_increment'],
						'cat'	 => ['VCHAR', ''],
						'title'	 => ['VCHAR', ''],
						'type'	 => ['VCHAR', ''],
						'parent' => ['BOOL', 0],
						'auth'	 => ['VCHAR', ''],
						'route'	 => ['VCHAR', ''],
						'crud'	 => ['BOOL', 0],
						'icon'	 => ['VCHAR', ''],
					],
					'PRIMARY_KEY' => ['id'],
				],
				$this->table_prefix . gzo::PLUGINS => [
					'COLUMNS' => [
						'id'	   => ['UINT', null, 'auto_increment'],
						'name'	   => ['VCHAR', ''],
						'ext_name' => ['VCHAR', ''],
						'position' => ['UINT', 0],
						'section'  => ['VCHAR', ''],
					],
					'PRIMARY_KEY' => ['id'],
				],
				$this->table_prefix . gzo::PLUGINS_ON_PAGE => [
					'COLUMNS' => [
						'id'		=> ['UINT', null, 'auto_increment'],
						'name'		=> ['VCHAR', ''],
						'page_name' => ['VCHAR', ''],
						'active'	=> ['BOOL', 0],
					],
					'PRIMARY_KEY' => ['id'],
				],
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function revert_schema(): array
	{
		return [
			'drop_tables' => [
				$this->table_prefix . gzo::AREA,
				$this->table_prefix . gzo::PLUGINS,
				$this->table_prefix . gzo::PLUGINS_ON_PAGE,
			],
		];
	}
}
