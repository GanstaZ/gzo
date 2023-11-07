<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\blocks;

class data
{
	/** @var array Contains validated blocks data */
	protected static array $data = [];

	public function set_template_data(string $section, array $data): void
	{
		$this->get($section) ? self::$data[$section] = array_merge(self::$data[$section], $data) : self::$data[$section] = $data;
	}

	public function get(string $section): array
	{
		return self::$data[$section] ?? [];
	}
}
