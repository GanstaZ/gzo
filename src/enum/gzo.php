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
	public const DATE_FORMAT = 'Y-m-d H:i';
	public const MAIN_MIGRATION = '\ganstaz\gzo\migrations\v40\m1_main';

	// Tables
	public const PLUGINS = 'gzo_plugins';
	public const BLOCKS = 'gzo_blocks';
	public const PAGES = 'gzo_pages';
}
