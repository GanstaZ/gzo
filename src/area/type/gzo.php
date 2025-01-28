<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\area\type;

#[\ganstaz\gzo\src\attribute\auth('ADMIN', 'a_', 'GZO_NO_ADMIN', 403)]
class gzo extends area_base
{
	public function load_navigation(): void
	{
		$this->add_language('area_gzo', 'ganstaz/gzo')
			->create_view('GZO_DASHBOARD', 'gzo_main');
	}
}
