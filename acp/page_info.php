<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\acp;

/**
* ACP pages info module
*/
class page_info
{
	public function module()
	{
		return [
			'filename' => '\ganstaz\gzo\acp\page_module',
			'title'	   => 'ACP_GZO_PAGE_TITLE',
			'modes'	   => [
				'page' => ['title' => 'ACP_GZO_PAGE', 'auth' => 'ext_ganstaz/gzo && acl_a_board', 'cat' => ['ACP_GZO_PAGE_TITLE'],
				],
			],
		];
	}
}
