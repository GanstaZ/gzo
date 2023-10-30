<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\entity;

use phpbb\di\service_collection;
use phpbb\cache\service as cache;

/**
* Entity manager
*/
final class manager
{
	/** @var array Contains entity types */
	private static array $types = [];

	public function __construct(
		private service_collection $collection,
		private cache $cache
	)
	{
		if ($collection)
		{
			foreach ($collection as $type)
			{
				self::$types[$type->get_type()] = $type;
			}
		}
	}

	/**
	* Get entity type by name
	*/
	public function type(string $name): object
	{
		return self::$types[$name] ?? (object) [];
	}

	/**
	* Get all available types
	*/
	public function available(): array
	{
		return array_keys(self::$types) ?? [];
	}
}
