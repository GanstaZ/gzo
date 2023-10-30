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

interface block_interface
{
	/**
	* Get block data
	*	['section' => '','ext_name' => '',]
	*/
	public function get_block_data(): array;

	/**
	* Set load to active [Default should be true, if block load function is not empty]
	*/
	public function set_active(bool $set): void;

	/**
	* Check if load method is required [Default is true]
	*/
	public function is_load_active(): bool;

	/**
	* Load block
	*/
	public function load(): void;
}
