<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\tabs\type;

/**
* GZO Web: interface for tabs
*/
interface tabs_interface
{
	/**
	* Set tab name
	*
	* @param string $name Name of the tab
	* @return void
	*/
	public function set_name(string $name);

	/**
	* Returns the name of the tab
	*
	* @return string Name of the tab
	*/
	public function get_name();

	/**
	* Returns the namespace
	*
	* @return string Twig namespace
	*/
	public function namespace();

	/**
	* Load current user
	*
	* @param string $username Name of the member
	* @return void
	*/
	public function load(string $username);
}
