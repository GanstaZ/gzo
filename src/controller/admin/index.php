<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\controller\admin;

use ganstaz\gzo\src\attribute\auth;
use ganstaz\gzo\src\controller\abstract_controller;

#[auth('ROLE_ADMIN', 'a_board', 'GZO_NO_ADMIN', 403)]
class index extends abstract_controller
{
	public function main(): \Symfony\Component\HttpFoundation\Response
	{
		//$this->helper->language->add_lang('info_acp_global', 'ganstaz/gzo');
		//$this->helper->assign_breadcrumb('GZO_MAIN_PAGE', 'gzo_main');

		return $this->helper->controller_helper->render('admin/index.twig', $this->helper->language->lang('GZO_MAIN_PAGE'));
	}
}
