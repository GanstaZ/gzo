<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\area\type;

#[\ganstaz\gzo\src\attribute\auth('ADMIN', 'a_', 'GZO_NO_ADMIN', 403)]
class gzo extends area_base
{
	public function load_navigation($type, $route): void
	{
		$this->helper->language->add_lang('info_acp_global', 'ganstaz/gzo');

		$this->set_category_icon('ACP_GZO_TITLE', 'cogs')
			->build_navigation($type, 'GZO_MAIN_PAGE', $route);
	}

	public function get_name(): string
	{
		return 'gzo';
	}
}
