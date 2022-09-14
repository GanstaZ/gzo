<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\controller;

/**
* GZ Web: news controller
*/
class news_controller extends base
{
	/**
	* News controller for routes:
	*
	*	 /news/{id}
	*	 /news/{id}/page/{page}
	*
	* @param int $id
	* @param int $page
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle(int $id, int $page): \Symfony\Component\HttpFoundation\Response
	{
		$this->news->set_page($page)
			->trim_news(true)
			->base($id);

		return $this->helper->render('news.html', $this->language->lang('VIEW_NEWS', $id), 200, true);
	}

	/**
	* Article controller for route /article/{aid}
	*
	* @param int $aid
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function article(int $aid): \Symfony\Component\HttpFoundation\Response
	{
		$this->news->get_article($aid);

		return $this->helper->render('article.html', $this->language->lang('VIEW_ARTICLE', $aid), 200, true);
	}
}
