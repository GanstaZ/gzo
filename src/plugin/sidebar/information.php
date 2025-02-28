<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\plugin\sidebar;

use ganstaz\gzo\src\enum\gzo;
use ganstaz\gzo\src\event\events;
use ganstaz\gzo\src\plugin\plugin;

class information extends plugin
{
	/**
	* {@inheritdoc}
	*/
	public function load_plugin(): void
	{
		/** @event events::GZO_INFORMATION_BEFORE */
		$this->dispatcher->trigger_event(events::GZO_INFORMATION_BEFORE);

		// Set template vars
		$this->template->assign_vars([
			'phpbb_version' => (string) PHPBB_VERSION,
			'gzo_version'	=> (string) gzo::VERSION,
			'gzo_style'		=> (string) gzo::STYLE,
		]);
	}
}
