<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\menu;

use ganstaz\gzo\src\contract\area_navigation;

final class admin implements area_navigation
{
	public const GZO_CAT = 'ACP_GZO_TITLE';
	public const GZO_ICON = 'cogs';

	public static function items(): array
	{
		return [
			[
				'title' => 'ACP_GZO_MAIN',
				'auth'  => 'acl_a_extensions',
				'cat'   => 'GZO_DASHBOARD',
				'route' => 'gzo_main',
				'icon'  => 'home',
			],
			[
				'title' => 'ACP_GZO_GLOBAL',
				'auth'  => 'acl_a_extensions',
				'route' => 'gzo_settings',
				'icon'  => 'globe',
			],
			[
				'title' => 'ACP_GZO_BLOCKS',
				'auth'  => 'acl_a_styles',
				'route' => 'gzo_blocks',
				'icon'  => 'cube',
			],
			[
				'title' => 'ACP_GZO_PAGE',
				'auth'  => 'acl_a_styles',
				'route' => 'gzo_pages',
				'icon'  => 'cog',
			],
		];
	}
}
