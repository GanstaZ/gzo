<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\plugin\blocks;

interface block_interface
{
	/**
	* Get block data
	*	['section' => '','ext_name' => '',]
	*/
	public function get_block_data(): array;
}
