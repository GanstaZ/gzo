<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\entity\type;

use phpbb\config\config as phpbb_config;

/**
* Entity type config
*/
final class config implements type_interface
{
	public function __construct(private phpbb_config $phpbb_config)
	{
	}

	/**
	* {@inheritdoc}
	*/
	public function get_type(): string
	{
		return 'config';
	}

	public function set(array $data)
	{
		foreach ($data as $key => $value)
		{
			// TODO: To be removed
			var_dump($key . ' ' . $value);

			$this->phpbb_config->set($key, $value);
		}
	}
}
