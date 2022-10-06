<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\controller;

use phpbb\event\dispatcher;
use ganstaz\web\model\main;

/**
* GZO Web: forum index controller
*/
class forum extends base
{
	/** @var dispatcher */
	protected $dispatcher;

	/** @var main */
	protected $main;

	/**
	* Constructor
	*
	* @param info $info Forum info helper object
	*/
	public function __construct($config, $helper, $language, $user, $posts, $root_path, $php_ext, $dispatcher, $main)
	{
		parent::__construct($config, $helper, $language, $user, $posts, $root_path, $php_ext);

		$this->dispatcher = $dispatcher;
		$this->main = $main;
	}

	/**
	* Index controller
	*
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle(): \Symfony\Component\HttpFoundation\Response
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

		return $this->helper->render('forum.twig', $this->language->lang($page_title), 200, true);
	}
}
