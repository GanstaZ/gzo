<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\area;

use phpbb\di\service_collection;

class loader
{
	private array $areas = [];

	public function __construct(private service_collection $collection)
	{
		if ($collection)
		{
			foreach ($collection as $area)
			{
				$this->areas[$area->get_name()] = $area;
			}
		}
	}

	public function is_area_available(string $name): bool
	{
		return isset($this->areas[$name]);
	}

	public function get_area(string $name): object
	{
		return $this->areas[$name];
	}

	public function all(): array
	{
		return $this->areas;
	}
}
