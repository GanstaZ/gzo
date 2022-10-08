<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, https://www.github.com/GanstaZ/
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
	'GZO_VERSION'			 => 'Current version',
	'MAIN_ID'				 => 'Main page id',
	'NEWS_ID'				 => 'News id',
	'ENABLE_NEWS_LINK'		 => 'News link in navigation',
	'GZO_PAGINATION'		 => 'Pagination',
	'ENABLE_PROFILE_TABS'	 => 'Profile tabs',
	'LIMIT'					 => 'News limit',
	'USER_LIMIT'			 => 'User limit',
	'LIMIT_EXPLAIN'			 => 'Default 5 & Max 10',
	'TITLE_LENGTH'			 => 'Trim title',
	'TITLE_LENGTH_EXPLAIN'	 => 'Default 26 & Max 50',
	'CONTENT_LENGTH'		 => 'Trim text',
	'CONTENT_LENGTH_EXPLAIN' => 'Default 150 & Max 250',

	// Blocks settings
	'GZO_SECTIONS'			 => 'Global blocks settings',
	'GZO_BLOCKS'			 => 'Blocks',
	'GZO_BLOCKS_EXPLAIN'	 => 'Will hide or show blocks, where blocks are autoloaded by page settings.<br>Manual loading of blocks is not affected.',
	'GZO_RIGHT'				 => 'Right',
	'GZO_LEFT'				 => 'Left',
	'GZO_MIDDLE'			 => 'Middle',
	'GZO_TOP'				 => 'Top',
	'GZO_BOTTOM'			 => 'Bottom',
]);
