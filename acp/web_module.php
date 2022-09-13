<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\acp;

/**
* GZ Web ACP settings module
*/
class web_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->tpl_name = 'acp_web';
		$this->page_title = $phpbb_container->get('language')->lang('ACP_GZ_WEB_TITLE');

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('ganstaz.web.admin.controller')
			->set_page_url($this->u_action);

		$admin_controller->display_web();
	}
}
