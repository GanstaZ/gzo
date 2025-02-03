<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\enum;

final class gzo
{
	// Common
	public const VERSION = '4.0.0-dev';
	public const STYLE = 'Tempest';
	public const VENDOR = 'ganstaz';
	public const EXT_NAME = 'ganstaz_gzo';
	public const DATE_FORMAT = 'Y-m-d H:i';
	public const MAIN_MIGRATION = '\ganstaz\gzo\migrations\v40\m1_main';

	// Area
	public const TYPE = 'gzo';
	public const AREAS = [
		'main'	  => 'GZO_DASHBOARD',
		'config'  => 'GZO_CONFIG',
		'plugins' => 'GZO_PLUGINS',
	];

	// Tables
	public const AREA = 'gzo_area';
	public const PLUGINS = 'gzo_plugins';
	public const PLUGINS_ON_PAGE = 'gzo_plugins_on_page';

	// Plugins
	public const PLUGIN = [
		'profile' => 'ganstaz_mini_profile',
		'info' => 'ganstaz_information',
		'group' => 'ganstaz_group',
		'poster' => 'ganstaz_top_posters',
		'posts' => 'ganstaz_recent_posts',
		'topics' => 'ganstaz_recent_topics',
		'online' => 'ganstaz_online',
		'announcement' => 'ganstaz_announcement',
	];

	// Pages
	public const PAGE = [
		'app' => 'app',
	];

	// Sections
	public const SECTION = [
		'top' => 'gzo_top',
		'side' => 'gzo_side',
		'block' => 'gzo_block',
		'bottom' => 'gzo_bottom',
	];
}
