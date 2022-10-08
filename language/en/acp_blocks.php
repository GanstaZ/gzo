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
	'BLOCK'			=> 'Block name',
	'EXT_NAME'		=> 'Extension name',
	'SERVICE_NAME'	=> 'Service name',
	'NAME'			=> 'Name',
	'SERVICE'		=> 'Service',
	'SECTION'		=> 'Section',
	'ADD_ERROR'		=> '%s Error%s',
	'CAT_ERROR'		=> ' (%s) does not exist',
	'VAR_EMPTY'		=> 'None',
	'EXT_ERROR'		=> ' (%s) is not enabled/available',
	'PRE_ERROR'		=> ' Prefix does not match',
	'SER_ERROR'		=> ' Incorrect service name',
	'NOT_AVAILABLE' => ' Service not available',
	'CHECK_TO'		=> 'Check to install ',

	// Sections
	'GZO_RIGHT'		=> 'Right side',
	'GZO_LEFT'		=> 'Left side',
	'GZO_TOP'		=> 'Top',
	'GZO_MIDDLE'	=> 'Middle',
	'GZO_BOTTOM'	=> 'Bottom',

	// Blocks
	'GANSTAZ_NEWS'			=> 'News',
	'GANSTAZ_MINI_PROFILE'	=> 'Mini profile',
	'GANSTAZ_INFORMATION'	=> 'Information',
	'GANSTAZ_THE_TEAM'		=> 'The team',
	'GANSTAZ_TOP_POSTERS'	=> 'Top posters',
	'GANSTAZ_RECENT_POSTS'	=> 'Recent posts',
	'GANSTAZ_RECENT_TOPICS' => 'Recent topics',
	'GANSTAZ_WHOS_ONLINE'	=> 'Who is online',

	'BLOCKS'			=> ' block%s',
	'BLOCK_POSITION'	=> 'Position',
	'BLOCK_SECTION'		=> 'Change section',
	'DUPLICATE'			=> 'Duplicate',
	'ADD_BLOCK'			=> '%s New block%s available',
	'PURGE_BLOCK'		=> 'Purge required! Click submit to remove: %s.',
]);
