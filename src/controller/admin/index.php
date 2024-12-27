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
use ganstaz\gzo\src\controller\helper;
use ganstaz\gzo\src\entity\manager as em;
use ganstaz\gzo\src\enum\gzo;
use ganstaz\gzo\src\form\form;

use phpbb\config\config;
use phpbb\event\dispatcher;
use Symfony\Component\HttpFoundation\Response;

#[\ganstaz\gzo\src\attribute\auth('ROLE_ADMIN', 'a_board', 'GZO_NO_ADMIN', 403)]
class index extends abstract_controller
{
	public function __construct(
		dispatcher $dispatcher,
		helper $helper,
		em $em,
		form $form,
		$root_path,
		$php_ext,
		private readonly config $config
	)
	{
		parent::__construct($dispatcher, $helper, $em, $form, $root_path, $php_ext);
	}

	public function main(): Response
	{
		$this->helper->language->add_lang('area_gzo', 'ganstaz/gzo');
		//$this->helper->assign_breadcrumb('GZO_MAIN_PAGE', 'gzo_main');

		$this->helper->twig->assign_vars([
			'GZO_VERSION'      => gzo::VERSION,
			'GZO_STYLE'        => gzo::STYLE,

			'PHP_VERSION_INFO' => PHP_VERSION,
			'BOARD_VERSION'    => $this->config['version'],
		]);

		return $this->helper->controller_helper->render('admin/index.twig', $this->helper->language->lang('GZO_MAIN_PAGE'));
	}
}
