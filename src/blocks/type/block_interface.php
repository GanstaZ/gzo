<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\blocks\type;

/**
* Interface for blocks
*/
interface block_interface
{
	/**
	* Get block data
	*	['section' => '','ext_name' => '',]
	*
	* @return array
	*/
	public function get_block_data();

	/**
	* Set load to active [Default should be true, if block load function is not empty]
	*
	* @param bool $set to true or false
	* @return void
	*/
	public function set_active(bool $set);

	/**
	* Check if load method is required [Default is true]
	*
	* @return bool
	*/
	public function is_load_active();

	/**
	* Load block
	*
	* @return void
	*/
	public function load();
}
