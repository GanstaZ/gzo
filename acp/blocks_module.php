<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\acp;

/**
* ACP blocks module
*/
class blocks_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->tpl_name = 'acp_blocks';
		$this->page_title = $phpbb_container->get('language')->lang('ACP_GZO_BLOCKS_TITLE');

		// Get an instance of the admin blocks controller
		$admin_controller = $phpbb_container->get('ganstaz.gzo.admin.block_controller')
			->set_page_url($this->u_action);

		$admin_controller->display_blocks();
	}
}
