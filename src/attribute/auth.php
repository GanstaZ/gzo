<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class auth
{
	public function __construct(
		/**
		* First argument
		*/
		public string $role,

		/**
		* Second argument
		*/
		public string $option,

		/**
		* Exception message
		*/
		public ?string $message = null,

		/**
		* Exception status code
		*/
		public ?int $status_code = null,
	)
	{
	}
}
