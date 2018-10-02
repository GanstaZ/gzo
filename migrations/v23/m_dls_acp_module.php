<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\migrations\v23;

class m_dls_acp_module extends \phpbb\db\migration\migration
{
	/**
	* This migration depends on dls's m_dls_main migration
	* already being installed.
	*/
	static public function depends_on()
	{
		return ['\dls\web\migrations\v23\m_dls_main'];
	}

	public function update_data()
	{
		return [
			// Add a parent module (ACP_DLS_WEB_TITLE) to the Extensions tab (ACP_CAT_DOT_MODS)
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_DLS_WEB_TITLE'
			]],
			// Add our main_modules to the parent module (ACP_DLS_WEB_TITLE)
			['module.add', [
				'acp',
				'ACP_DLS_WEB_TITLE',
				[
					'module_basename' => '\dls\web\acp\web_module', 'modes' => ['settings'],
				],
			]],
			['module.add', [
				'acp',
				'ACP_DLS_WEB_TITLE',
				[
					'module_basename' => '\dls\web\acp\blocks_module', 'modes' => ['blocks'],
				],
			]],
		];
	}
}
