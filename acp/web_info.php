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
* GZO Web: ACP module info
*/
class web_info
{
	public function module()
	{
		return [
			'filename' => '\ganstaz\web\acp\web_module',
			'title'	   => 'ACP_GZO_WEB_TITLE',
			'modes'	   => [
				'settings' => ['title' => 'ACP_GZO_WEB', 'auth' => 'ext_ganstaz/web && acl_a_board', 'cat' => ['ACP_GZO_WEB_TITLE'],
				],
			],
		];
	}
}
