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
* GZ Web index controller
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

		return $this->helper->render('index.html', $this->language->lang('MAIN', $id), 200, true);
	}
}
