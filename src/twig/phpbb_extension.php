<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\twig;

use phpbb\template\twig\extension;

class phpbb_extension extends extension
{
	/**
	* Returns the token parser instance to add to the existing list.
	*/
	public function getTokenParsers(): array
	{
		return [
			new \phpbb\template\twig\tokenparser\defineparser,
			new \phpbb\template\twig\tokenparser\includeparser,
			new \phpbb\template\twig\tokenparser\includejs,
			new \phpbb\template\twig\tokenparser\includecss,
			new \ganstaz\gzo\src\twig\tokenparser\event($this->environment),
		];
	}
}
