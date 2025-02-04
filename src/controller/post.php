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

use phpbb\exception\http_exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class post extends abstract_controller
{
	/**
	* Post controller for /post/article{fid}
	*	  Redirects to right forum's posting page
	*/
	public function handle(int $fid): RedirectResponse
	{
		// Borrowed from Ideas extension (phpBB)
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			throw new http_exception(404, 'LOGIN_REQUIRED');
		}

		$params = [
			'mode' => 'post',
			'f'	   => $fid,
		];

		$url = append_sid(generate_board_url() . "/posting.{$this->php_ext}", $params, false);

		return new RedirectResponse($url);
	}
}
