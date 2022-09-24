<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
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
	'GZ_VERSION'			 => 'Current version',
	'MAIN_ID'				 => 'Main page id',
	'NEWS_ID'				 => 'News id',
	'ENABLE_NEWS_LINK'		 => 'News link in navigation',
	'GZ_PAGINATION'		     => 'Pagination',
	'ENABLE_PROFILE_TABS'    => 'Profile tabs',
	'LIMIT'					 => 'News limit',
	'USER_LIMIT'			 => 'User limit',
	'LIMIT_EXPLAIN'			 => 'Default 5 & Max 10',
	'TITLE_LENGTH'			 => 'Trim title',
	'TITLE_LENGTH_EXPLAIN'	 => 'Default 26 & Max 50',
	'CONTENT_LENGTH'		 => 'Trim text',
	'CONTENT_LENGTH_EXPLAIN' => 'Default 150 & Max 250',

	// Blocks settings
	'GZ_SECTIONS'			 => 'Global blocks settings',
	'GZ_BLOCKS'			     => 'Blocks',
	'GZ_BLOCKS_EXPLAIN'	     => 'Will hide or show blocks, where blocks are autoloaded by page settings.<br>Manual loading of blocks is not affected.',
	'GZ_SPECIAL'			 => 'Special',
	'GZ_RIGHT'				 => 'Right',
	'GZ_LEFT'				 => 'Left',
	'GZ_MIDDLE'			     => 'Middle',
	'GZ_TOP'				 => 'Top',
	'GZ_BOTTOM'			     => 'Bottom',
]);
