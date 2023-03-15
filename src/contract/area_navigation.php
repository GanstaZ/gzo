<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\contract;

/**
* Interface for area navigation
*/
interface area_navigation
{
	public const GZO_CAT = '';
	public const GZO_ICON = 'home';

	/**
	* Navigation data
	*/
	public static function items(): array;
}
