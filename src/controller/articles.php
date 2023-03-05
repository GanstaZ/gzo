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

/**
* Articles controller
*/
class articles extends base
{
	/**
	* Articles controller for routes:
	*
	*	 /articles/{id}
	*	 /articles/{id}/page/{page}
	*
	* @param int $id
	* @param int $page
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle(int $id, int $page): \Symfony\Component\HttpFoundation\Response
	{
		$this->posts->set_page($page)
			->trim_messages(true)
			->base($id);

		return $this->helper->render('news.twig', $this->language->lang('VIEW_NEWS', $id), 200, true);
	}

	/**
	* Article controller for route /article-full/{aid}
	*
	* @param int $aid
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\RedirectResponse A Symfony Redirect Response object
	*/
	public function article(int $aid): \Symfony\Component\HttpFoundation\RedirectResponse
	{
		$row = $this->posts->get_forum_id($aid);

		if (!$row)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_TOPICS', [$row]);
		}

		$params = [
			'f' => (int) $row['forum_id'],
			't' => $aid
		];

		$url = append_sid(generate_board_url() . "/viewtopic.{$this->php_ext}", $params, false);

		return new \Symfony\Component\HttpFoundation\RedirectResponse($url);
	}

	/**
	* First post controller (without any replies) for route /article/{aid}
	*
	* @param int $aid
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function first_post(int $aid): \Symfony\Component\HttpFoundation\Response
	{
		$this->posts->get_first_post($aid);

		return $this->helper->render('article.twig', $this->language->lang('VIEW_ARTICLE', $aid), 200, true);
	}
}
