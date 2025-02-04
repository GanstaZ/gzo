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

use ganstaz\gzo\src\helper\controller_helper;
use ganstaz\gzo\src\entity\manager as em;
use ganstaz\gzo\src\form\form;
use ganstaz\gzo\src\plugin\article\posts;
use phpbb\config\config;
use phpbb\event\dispatcher;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\HttpFoundation\Response;

class index extends abstract_controller
{
	public function __construct(
		config $config,
		dispatcher $dispatcher,
		language $language,
		template $template,
		user $user,
		controller_helper $controller_helper,
		em $em,
		form $form,
		$root_path,
		$php_ext,
		private posts $posts
	)
	{
		parent::__construct($config, $dispatcher, $language, $template, $user, $controller_helper, $em, $form, $root_path, $php_ext);
	}

	public function handle(): Response
	{
		$id = (int) $this->config['gzo_main_fid'];

		$this->posts->trim_messages(true)
			->base($id);

		$data = $this->posts->breadcrumb;
		$this->controller_helper->assign_breadcrumb($data['name'], $data['route'], $data['params']);

		return $this->controller_helper->render('index.twig', $this->language->lang('HOME', $id), 200, true);
	}
}
