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

	// News settings
	'ACP_NEWS_LEGEND'		  => 'News settings',
	'ACP_NEWS_ENABLE'		  => 'Enable forum',
	'ACP_NEWS_ENABLE_EXPLAIN' => 'Set <strong>Yes</strong> to enable this forum for news.',
]);
