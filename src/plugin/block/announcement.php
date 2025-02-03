<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\plugin\block;

use ganstaz\gzo\src\plugin\plugin_base;

class announcement extends plugin_base
{
	/**
	* {@inheritdoc}
	*/
	public function load_plugin(): void
	{
		// Set template vars
		$this->template->assign_vars([
			'test_block' => 'Testing announcement',
		]);
	}
}
