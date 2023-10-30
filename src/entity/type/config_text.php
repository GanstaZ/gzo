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

use phpbb\config\db_text;

/**
* Entity type db_text
*/
final class config_text implements type_interface
{
	public function __construct(private db_text $config_text)
	{
	}

	/**
	* {@inheritdoc}
	*/
	public function get_type(): string
	{
		return 'config_text';
	}
}
