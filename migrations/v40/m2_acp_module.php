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

class m2_acp_module extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	public static function depends_on()
	{
		return [gzo::MAIN_MIGRATION];
	}

	/**
	* Add the initial data in the database
	*/
	public function update_data(): array
	{
		return [
			// Add a parent module (ACP_GZO_TITLE) to the Extensions tab (ACP_CAT_DOT_MODS)
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_GZO_TITLE'
			]],
			// Add our main_modules to the parent module (ACP_GZO_TITLE)
			['module.add', [
				'acp',
				'ACP_GZO_TITLE',
				[
					'module_basename' => '\ganstaz\gzo\acp\global_module', 'modes' => ['settings'],
				],
			]],
			['module.add', [
				'acp',
				'ACP_GZO_TITLE',
				[
					'module_basename' => '\ganstaz\gzo\acp\blocks_module', 'modes' => ['blocks'],
				],
			]],
			['module.add', [
				'acp',
				'ACP_GZO_TITLE',
				[
					'module_basename' => '\ganstaz\gzo\acp\page_module', 'modes' => ['page'],
				],
			]],
		];
	}
}
