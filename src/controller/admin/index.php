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

use ganstaz\gzo\src\controller\abstract_controller;
use ganstaz\gzo\src\enum\gzo;
use Symfony\Component\HttpFoundation\Response;

#[\ganstaz\gzo\src\attribute\auth('ROLE_ADMIN', 'a_board', 'GZO_NO_ADMIN', 403)]
class index extends abstract_controller
{
	public function main(): Response
	{
		// $this->helper->language->add_lang('area_gzo', 'ganstaz/gzo');
		//$this->helper->assign_breadcrumb('GZO_MAIN_PAGE', 'gzo_main');

		$this->template->assign_vars([
			'GZO_VERSION'      => gzo::VERSION,
			'GZO_STYLE'        => gzo::STYLE,

			'PHP_VERSION_INFO' => PHP_VERSION,
			'BOARD_VERSION'    => PHPBB_VERSION,
		]);

		return $this->controller_helper->render('admin/index.twig', $this->language->lang('GZO_MAIN_PAGE'));
	}
}
