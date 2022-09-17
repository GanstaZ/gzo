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

use phpbb\exception\http_exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
* GZ Web: post controller
*/
class post extends base
{
	/**
	* Controller for /post/article/fid
	* Redirects to right forum's posting page.
	*
	* @param int $forum_id
	* @throws http_exception
	* @return RedirectResponse A Symfony Response object
	*/
	public function post($fid): \Symfony\Component\HttpFoundation\RedirectResponse
	{
		// Borrowed from Ideas extension (phpBB)
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			throw new http_exception(404, 'LOGGED_OUT');
		}

		$params = [
			'mode' => 'post',
			'f'    => $fid,
		];

		$url = append_sid(generate_board_url() . "/posting.{$this->php_ext}", $params, false);

		return new RedirectResponse($url);
	}
}
