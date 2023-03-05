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
* ACP global info module
*/
class global_info
{
	public function module()
	{
		return [
			'filename' => '\ganstaz\gzo\acp\global_module',
			'title'	   => 'ACP_GZO_TITLE',
			'modes'	   => [
				'settings' => ['title' => 'ACP_GZO_GLOBAL', 'auth' => 'ext_ganstaz/gzo && acl_a_board', 'cat' => ['ACP_GZO_TITLE'],
				],
			],
		];
	}
}
