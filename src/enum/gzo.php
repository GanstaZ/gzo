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
		'profile' => self::VENDOR . '_mini_profile',
		'info' => self::VENDOR . '_information',
		'group' => self::VENDOR . '_group',
		'poster' => self::VENDOR . '_top_posters',
		'posts' => self::VENDOR . '_recent_posts',
		'topics' => self::VENDOR . '_recent_topics',
		'online' => self::VENDOR . '_online',
		'announcement' => self::VENDOR . '_announcement',
	];

	// Pages
	public const PAGE = [
		'main' => 'app',
	];

	// Sections
	public const SECTION = [
		'top' => 'gzo_top',
		'side' => 'gzo_side',
		'block' => 'gzo_block',
		'bottom' => 'gzo_bottom',
	];
}
