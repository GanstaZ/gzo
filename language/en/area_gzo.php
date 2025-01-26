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
	'GZO_MAIN_PAGE_DESC' => 'Welcome to GZO Admin Dashboard!',

	// Main category
	'GZO_GLOBAL'   => 'GZO CMS',

	'GZO_SETTINGS' => 'Global settings',
	'GZO_BLOCKS'   => 'Manage blocks',
	'GZO_PAGES'	   => 'Page settings',

	'GZO_PHP'	   => 'PHP version',
	'GZO_BOARD'	   => 'Board version',
]);
