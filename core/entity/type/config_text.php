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

use phpbb\config\db_text;

/**
* Entity type db_text
*/
final class config_text implements type_interface
{
	/** @var db_text */
	private object $config_text;

	/**
	* Constructor
	*
	* @param db_text $config_text Config text object
	*/
	public function __construct(db_text $config_text)
	{
		$this->config_text = $config_text;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_type(): string
	{
		return 'config_text';
	}
}
