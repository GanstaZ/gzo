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
	// Web module
	'ACP_GZ_WEB_TITLE'		  => 'GZ Web CMS',
	'ACP_GZ_WEB'			  => 'GZ Web settings',

	// Blocks module
	'ACP_GZ_BLOCKS_TITLE'	  => 'GZ Blocks module',
	'ACP_GZ_BLOCKS'		      => 'GZ blocks manager',

	// Blocks page module
	'ACP_GZ_PAGE_TITLE'	      => 'GZ Page module',
	'ACP_GZ_PAGE'			  => 'GZ page settings',

	// Plugin module
	'ACP_GZ_PLUGIN_TITLE'	  => 'GZ Plugin module',
	'ACP_GZ_PLUGIN'		      => 'GZ plugin manager',

	'ACP_GZ_SETTINGS_SAVED'   => 'Settings have been saved successfully!',

	// News settings
	'ACP_NEWS_LEGEND'		  => 'News settings',
	'ACP_NEWS_ENABLE'		  => 'Enable forum',
	'ACP_NEWS_ENABLE_EXPLAIN' => 'Set <strong>Yes</strong> to enable this forum for news.',
]);
