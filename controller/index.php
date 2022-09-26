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

/**
* GZO Web: index controller
*/
class index extends base
{
	/**
	* Index controller
	*
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle(): \Symfony\Component\HttpFoundation\Response
	{
		// Set main page id
		$id = (int) $this->config['gz_main_fid'];

		$this->posts->trim_messages(true)
			->base($id);

		return $this->helper->render('index.twig', $this->language->lang('HOME', $id), 200, true);
	}
}
