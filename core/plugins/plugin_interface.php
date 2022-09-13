<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\plugins;

/**
* GZ Web: plugin interface
*/
interface plugin_interface
{
	/**
	* Returns the name of the plugin
	*
	* @return string Name of the plugin
	*/
	public function get_name();

	/**
	* Returns the type of the plugin
	*
	* @return string Type of the plugin
	*/
	public function get_type();
}
