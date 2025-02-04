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
use ganstaz\gzo\src\plugin\article\posts;
use phpbb\event\dispatcher;
use Symfony\Component\HttpFoundation\Response;

class articles extends abstract_controller
{
	public function __construct(
		dispatcher $dispatcher,
		helper $helper,
		em $em,
		form $form,
		$root_path,
		$php_ext,
		private readonly posts $posts
	)
	{
		parent::__construct($dispatcher, $helper, $em, $form, $root_path, $php_ext);
	}

	/**
	* Articles controller for routes:
	*
	*	 /articles/{id}
	*	 /articles/{id}/page/{page}
	*
	*/
	public function handle(int $id, int $page): Response
	{
		$this->posts->set_page_offset($page)
			->trim_messages(true)
			->base($id);

		$data = $this->posts->breadcrumb;
		$this->helper->assign_breadcrumb($data['name'], $data['route'], $data['params']);

		return $this->helper->controller_helper->render('news.twig', $this->helper->language->lang('VIEW_NEWS', $id), 200, true);
	}

	/**
	* First post controller (without any replies) for route /article/{aid}
	*/
	public function first_post(int $aid): Response
	{
		$this->posts->trim_messages(false)
			->get_first_post($aid);

		$data = $this->posts->breadcrumb;
		$this->helper->assign_breadcrumb($data['name'], $data['route'], $data['params']);

		return $this->helper->controller_helper->render('article.twig', $this->helper->language->lang('VIEW_ARTICLE', $aid), 200, true);
	}
}
