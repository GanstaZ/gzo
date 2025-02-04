<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\helper;

use phpbb\controller\helper as phpbb_helper;

/**
* Controller helper class
*/
class controller_helper extends phpbb_helper
{
	public function add_language(string $name, string $path): self
	{
		$this->language->add_lang($name, $path);

		return $this;
	}

	public function assign_breadcrumb(string $name, string $route, array $params = []): self
	{
		$this->template->assign_block_vars('navlinks', [
			'FORUM_NAME'   => $this->language->lang($name),
			'U_VIEW_FORUM' => $this->route($route, $params),
		]);

		return $this;
	}
}
