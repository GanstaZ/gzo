<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\controller\member;

use phpbb\controller\helper as controller;
use phpbb\language\language;
use ganstaz\web\model\member\profile as model;

/**
* GZO Web: Member profile controller
*/
class profile
{
	/** @var controller helper */
	protected $controller;

	/** @var language */
	protected $language;

	/** @var profile */
	protected $model;

	/**
	* Constructor
	*
	* @param controller $controller Controller helper object
	* @param model      $model      Profile object
	*/
	public function __construct
	(
		controller $controller,
		language $language,
		model $model,
	)
	{
		$this->controller = $controller;
		$this->language   = $language;
		$this->model	  = $model;
	}

	/**
	* Profile controller
	*
	* @param string $username
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($username): \Symfony\Component\HttpFoundation\Response
	{
		// Load language strings
		$this->language->add_lang('memberlist');

		$this->model->view($username);

		return $this->controller->render('profile.twig', $this->language->lang('VIEWING_PROFILE', $username), 200, true);
	}
}
