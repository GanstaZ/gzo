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
	// Global language vars
	'ACP_GZO_SETTINGS_SAVED'  => 'Settings have been saved successfully!',
	'GZO_DASHBOARD' => 'Dashboard',

	// GZO admin area
	'GZO_CAT_ADMIN' => 'GZO Admin',

	// Settings module
	'ACP_GZO_TITLE'			  => 'GZO CMS',
	'ACP_GZO_GLOBAL'		  => 'Global settings',

	// Blocks module
	'ACP_GZO_BLOCKS_TITLE'	  => 'GZO Blocks module',
	'ACP_GZO_BLOCKS'		  => 'Manage blocks',

	// Page module
	'ACP_GZO_PAGE_TITLE'	  => 'GZO Page module',
	'ACP_GZO_PAGE'			  => 'Page settings',

	// News settings
	'ACP_NEWS_LEGEND'		  => 'News settings',
	'ACP_NEWS_ENABLE'		  => 'Enable forum',
	'ACP_NEWS_ENABLE_EXPLAIN' => 'Set <strong>Yes</strong> to enable this forum for news.',
]);
