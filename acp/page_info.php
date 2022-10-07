<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\acp;

/**
* GZO Web: ACP pages module info
*/
class page_info
{
	public function module()
	{
		return [
			'filename' => '\ganstaz\web\acp\page_module',
			'title'	   => 'ACP_GZO_PAGE_TITLE',
			'modes'	   => [
				'page' => ['title' => 'ACP_GZO_PAGE', 'auth' => 'ext_ganstaz/web && acl_a_board', 'cat' => ['ACP_GZO_PAGE_TITLE'],
				],
			],
		];
	}
}
