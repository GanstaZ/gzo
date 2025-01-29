<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\controller;

use ganstaz\gzo\src\controller\helper;
use ganstaz\gzo\src\entity\manager as em;
use ganstaz\gzo\src\form\form;
use ganstaz\gzo\src\plugins\article\posts;
use phpbb\config\config;
use phpbb\event\dispatcher;
use Symfony\Component\HttpFoundation\Response;

class index extends abstract_controller
{
	public function __construct(
		dispatcher $dispatcher,
		helper $helper,
		em $em,
		form $form,
		$root_path,
		$php_ext,
		private readonly config $config,
		private readonly posts $posts
	)
	{
		parent::__construct($dispatcher, $helper, $em, $form, $root_path, $php_ext);
	}

	public function handle(): Response
	{
		$id = (int) $this->config['gzo_main_fid'];

		$this->posts->trim_messages(true)
			->base($id);

		$data = $this->posts->breadcrumb;
		$this->helper->assign_breadcrumb($data[0], $data[1], $data[2]);

		return $this->helper->controller_helper->render('index.twig', $this->helper->language->lang('HOME', $id), 200, true);
	}
}
