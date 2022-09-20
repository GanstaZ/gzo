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
use ganstaz\web\core\tabs\manager;

/**
* GZO Web: Member profile controller
*/
class profile
{
	/** @var controller helper */
	protected $controller;

	/** @var language */
	protected $language;

	/** @var manager */
	protected $manager;

	/**
	* Constructor
	*
	* @param controller $controller Controller helper object
	* @param manager    $manager    Profile object
	*/
	public function __construct
	(
		controller $controller,
		language $language,
		manager $manager
	)
	{
		$this->controller = $controller;
		$this->language   = $language;
		$this->manager    = $manager;
	}

	/**
	* Profile controller
	*
	* @param string $username
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($username, $tab): \Symfony\Component\HttpFoundation\Response
	{
		// Load language strings
		$this->language->add_lang('memberlist');

		$this->manager->get($tab)->load($username);

		return $this->controller->render('profile.twig', $this->language->lang('VIEWING_PROFILE', $username), 200, true);
	}
}
