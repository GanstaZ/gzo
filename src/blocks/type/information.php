<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\blocks\type;

use ganstaz\gzo\src\enum\gzo;
use ganstaz\gzo\src\event\events;

/**
* Information block
*/
class information extends base
{
	/**
	* {@inheritdoc}
	*/
	public function get_block_data(): array
	{
		return [
			'section'  => 'gzo_right',
			'ext_name' => 'ganstaz_gzo',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
		/** @event events::GZO_INFORMATION_BEFORE */
		$this->dispatcher->trigger_event(events::GZO_INFORMATION_BEFORE);

		// Set template vars
		$this->twig->assign_vars([
			'phpbb_version' => (string) $this->config['version'],
			'gzo_version'	=> (string) gzo::VERSION,
			'gzo_style'		=> (string) gzo::STYLE,
		]);
	}
}
