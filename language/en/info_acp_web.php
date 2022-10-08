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
	// Web module
	'ACP_GZO_WEB_TITLE'		  => 'GZO Web CMS',
	'ACP_GZO_WEB'			  => 'GZO Web settings',

	// Blocks module
	'ACP_GZO_BLOCKS_TITLE'	  => 'GZO Blocks module',
	'ACP_GZO_BLOCKS'		  => 'GZO blocks manager',

	// Blocks page module
	'ACP_GZO_PAGE_TITLE'	  => 'GZO Page module',
	'ACP_GZO_PAGE'			  => 'GZO page settings',

	'ACP_GZO_SETTINGS_SAVED'  => 'Settings have been saved successfully!',

	// News settings
	'ACP_NEWS_LEGEND'		  => 'News settings',
	'ACP_NEWS_ENABLE'		  => 'Enable forum',
	'ACP_NEWS_ENABLE_EXPLAIN' => 'Set <strong>Yes</strong> to enable this forum for news.',
]);
