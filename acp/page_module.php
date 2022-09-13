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
* GZ Web ACP page module
*/
class page_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->tpl_name = 'acp_pages';
		$this->page_title = $phpbb_container->get('language')->lang('ACP_GZ_PAGE_TITLE');

		// Get an instance of the admin page controller
		$admin_controller = $phpbb_container->get('ganstaz.web.admin.page.controller')
			->set_page_url($this->u_action);

		$admin_controller->display_page();
	}
}
