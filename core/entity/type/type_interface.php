<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\entity\type;

/**
* Entity type interface
*/
interface type_interface
{
	/**
	* Returns the entity type
	*
	* @return string Entity type
	*/
	public function get_type();
}
