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
				$this->areas[$area->type] = $area;
			}
		}
	}

	public function available(string $type): bool
	{
		return isset($this->areas[$type]);
	}

	public function get(string $type): object
	{
		return $this->areas[$type];
	}

	public function all(): array
	{
		return array_keys($this->areas);
	}
}
