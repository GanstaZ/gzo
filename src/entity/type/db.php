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

use phpbb\db\driver\driver_interface;

/**
* Entity type db
*/
final class db implements type_interface
{
	/** @var driver_interface */
	protected object $db;

	/**
	* Constructor
	*
	* @param driver_interface $db Database object
	*/
	public function __construct(driver_interface $db)
	{
		$this->db = $db;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_type(): string
	{
		return 'db';
	}
}
