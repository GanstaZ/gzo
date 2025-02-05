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
use ganstaz\gzo\src\model\main;
use phpbb\config\config;
use phpbb\event\dispatcher;
use Symfony\Component\HttpFoundation\Response;

class forum extends abstract_controller
{
	public function __construct(
		config $config,
		dispatcher $dispatcher,
		controller_helper $controller_helper,
		em $em,
		form $form,
		$root_path,
		$php_ext,
		private readonly main $main
	)
	{
		parent::__construct($config, $dispatcher, $language, $template, $user, $controller_helper, $em, $form, $root_path, $php_ext);
	}

	/**
	* Index controller
	*/
	public function handle(): Response
	{
		$this->main->load();

		$page_title = ($this->config['board_index_text'] !== '') ? $this->config['board_index_text'] : $this->language->lang('INDEX');

		/**
		* You can use this event to modify the page title and load data for the index
		*
		* @event core.index_modify_page_title
		* @var	string	page_title		Title of the index page
		* @since 3.1.0-a1
		*/
		$vars = ['page_title'];
		extract($this->dispatcher->trigger_event('core.index_modify_page_title', compact($vars)));

		return $this->controller_helper->render('forum.twig', $this->language->lang($page_title), 200, true);
	}
}
