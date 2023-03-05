<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'GZO_MAIN_FID'		 => 'Change news id for main page',
	'GZO_NEWS_FID'		 => 'Change news id for news page',
	'GZO_NEWS_LINK'		 => 'Enable news link in navigation',
	'GZO_PAGINATION'	 => 'Enable pagination',
	'GZO_PROFILE_TABS'	 => 'Enable profile tabs',
	'GZO_LIMIT'			 => 'Change news limit',
	'GZO_USER_LIMIT'	 => 'Change user limit',

	'GZO_TITLE_LENGTH'	 => 'Trim title',
	'GZO_CONTENT_LENGTH' => 'Trim text',

	// Blocks settings
	'GZO_SECTIONS'		 => 'Global blocks settings',
	'GZO_BLOCKS'		 => 'Blocks',
	'GZO_BLOCKS_EXPLAIN' => 'Will hide or show blocks where blocks are autoloaded by page settings.<br>Manual loading of blocks is not affected.',
	'GZO_RIGHT'			 => 'Right',
	'GZO_LEFT'			 => 'Left',
	'GZO_MIDDLE'		 => 'Middle',
	'GZO_TOP'			 => 'Top',
	'GZO_BOTTOM'		 => 'Bottom',
]);
