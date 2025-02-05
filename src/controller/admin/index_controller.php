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

#[\ganstaz\gzo\src\security\attribute\auth('ROLE_ADMIN', 'a_', 'GZO_NO_ADMIN', 403)]
class index_controller extends abstract_controller
{
	#[\ganstaz\gzo\src\security\attribute\auth('ROLE_ADMIN', 'a_board', 'GZO_NO_ADMIN', 403)]
	public function index(): Response
	{
		$this->template->assign_vars([
			'GZO_VERSION'	   => gzo::VERSION,
			'GZO_STYLE'		   => gzo::STYLE,

			'PHP_VERSION_INFO' => PHP_VERSION,
			'BOARD_VERSION'	   => PHPBB_VERSION,
		]);

		return $this->controller_helper->render('admin/index.twig', $this->language->lang('GZO_MAIN_PAGE'));
	}
}
