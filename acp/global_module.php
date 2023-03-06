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
* ACP global module
*/
class global_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->tpl_name = 'acp_global';
		$this->page_title = $phpbb_container->get('language')->lang('ACP_GZO_TITLE');

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('ganstaz.gzo.admin.settings_controller')
			->set_page_url($this->u_action);

		$admin_controller->handle();
	}
}
