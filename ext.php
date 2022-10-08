<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web;

/**
* GZO Web: ext class
*/
class ext extends \phpbb\extension\base
{
	/**
	* Compare versions & enable if equal or greater than 3.3.5
	*
	* @return bool
	* @access public
	*/
	public function is_enableable()
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.3.5', '>=');
	}
}
