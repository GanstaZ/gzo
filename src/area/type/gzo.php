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

use phpbb\exception\http_exception;

class gzo extends area_base
{
	public function load(): void
	{
		//$this->lang->add_lang('acp/common');
		$this->helper->language->add_lang('info_acp_global', 'ganstaz/gzo');

		// TODO: #[is_authed attribute]
		if (!$this->auth->acl_get('a_'))
		{
			throw new http_exception(403, 'NO_ADMIN');
		}

		$this->set_category_icon('ACP_GZO_TITLE', 'cogs')
			->build_navigation('gzo', 'GZO_MAIN_PAGE', 'gzo_main');
	}

	public function get_name(): string
	{
		return 'gzo';
	}
}