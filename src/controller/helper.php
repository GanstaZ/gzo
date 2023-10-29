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

use phpbb\controller\helper as controller_helper;
use phpbb\language\language;
use phpbb\template\twig\twig;

/**
* Controller helper class
*/
class helper
{
	public function __construct(
		public readonly controller_helper $controller_helper,
		public readonly language $language,
		public readonly twig $twig,
	)
	{
	}

	public function assign_breadcrumb(string $name, string $route, array $params = []): self
	{
		$this->twig->assign_block_vars('navlinks', [
			'FORUM_NAME'   => $this->language->lang($name),
			'U_VIEW_FORUM' => $this->controller_helper->route($route, $params),
		]);

		return $this;
	}
}
