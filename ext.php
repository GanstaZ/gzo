<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo;

class ext extends \phpbb\extension\base
{
	/**
	* Compare versions & enable if equal or greater than 3.3.10
	*/
	public function is_enableable(): bool
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.3.10', '>=');
	}
}
