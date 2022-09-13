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
	'GZ_SPECIAL'	=> 'Special',
	'GZ_RIGHT'		=> 'Right side',
	'GZ_LEFT'		=> 'Left side',
	'GZ_TOP'		=> 'Top',
	'GZ_MIDDLE'	    => 'Middle',
	'GZ_BOTTOM'	    => 'Bottom',

	// Blocks
	'GZ_NEWS'			=> 'News',
	'GZ_MINI_PROFILE'	=> 'Mini profile',
	'GZ_INFORMATION'	=> 'Information',
	'GZ_THE_TEAM'		=> 'The team',
	'GZ_TOP_POSTERS'	=> 'Top posters',
	'GZ_RECENT_POSTS'	=> 'Recent posts',
	'GZ_RECENT_TOPICS'  => 'Recent topics',
	'GZ_WHOS_ONLINE'	=> 'Who is online',

	'BLOCKS'			=> ' block%s',
	'BLOCK_POSITION'	=> 'Position',
	'BLOCK_SECTION'		=> 'Change section',
	'DUPLICATE'			=> 'Duplicate',
	'ADD_BLOCK'			=> '%s New block%s available',
	'PURGE_BLOCK'		=> 'Purge required! Click submit to remove: %s.',
]);
