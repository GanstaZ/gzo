<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\acp;

/**
* ACP blocks info module
*/
class blocks_info
{
	public function module()
	{
		return [
			'filename' => '\ganstaz\gzo\acp\blocks_module',
			'title'	   => 'ACP_GZO_BLOCKS_TITLE',
			'modes'	   => [
				'blocks' => ['title' => 'ACP_GZO_BLOCKS', 'auth' => 'ext_ganstaz/gzo && acl_a_board', 'cat' => ['ACP_GZO_BLOCKS_TITLE'],
				],
			],
		];
	}
}
