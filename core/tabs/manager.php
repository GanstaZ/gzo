<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\tabs;

use phpbb\di\service_collection;

/**
* GZO Web: tabs manager
*/
class manager
{
	/** @var array Contains all available tabs */
	protected static $tabs = false;

	/**
	* Constructor
	*
	* @param service_collection $collection
	*/
	public function __construct(service_collection $collection)
	{
		$this->register_tabs($collection);
	}

	/**
	* Register all available tabs
	*
	* @param Service collection of tabs
	*/
	protected function register_tabs($collection): void
	{
		if (!empty($collection))
		{
			self::$tabs = [];
			foreach ($collection as $tab)
			{
				self::$tabs[$tab->get_name()] = $tab;
			}
		}
	}

	/**
	* Get tab type by name
	*
	* @param string $name Name of the tab
	* @return object
	*/
	public function get($name): object
	{
		return self::$tabs[$name] ?? (object) [];
	}

	/**
	* Get all available tabs
	*
	* @return array
	*/
	public function get_tabs(): array
	{
		return array_keys(self::$tabs) ?? [];
	}

	/**
	* Remove tab
	*
	* @param string $name Name of the tab we want to remove
	* @return void
	*/
	public function remove($name): void
	{
		if (isset(self::$tabs[$name]) || array_key_exists($name, self::$tabs))
		{
			unset(self::$tabs[$name]);
		}
	}
}
