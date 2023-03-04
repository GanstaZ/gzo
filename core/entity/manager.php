<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\entity;

use phpbb\di\service_collection;
use phpbb\cache\service as cache;

/**
* Entity manager
*/
final class manager
{
	/** @var cache */
	private object $cache;

	/** @var array Contains entity types */
	private static array $types = [];

	/**
	* Constructor
	*
	* @param service_collection $collection Entity types
	* @param cache				$cache Cache object
	*/
	public function __construct(service_collection $collection, cache $cache)
	{
		$this->cache = $cache;

		$this->register_entity_types($collection);
	}

	/**
	* Register all available types
	*
	* @param Service collection of entity types
	* @return void
	*/
	protected function register_entity_types($collection): void
	{
		if (!empty($collection))
		{
			foreach ($collection as $type)
			{
				self::$types[$type->get_type()] = $type;
			}
		}
	}

	/**
	* Get entity type by name
	*
	* @param string $name Name of the type
	* @return object
	*/
	public function type($name): object
	{
		return self::$types[$name] ?? (object) [];
	}

	/**
	* Get all available types
	*
	* @return array
	*/
	public function available(): array
	{
		return array_keys(self::$types) ?? [];
	}
}
