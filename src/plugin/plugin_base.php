<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\plugin;

use ganstaz\gzo\src\user\loader as users_loader;
use phpbb\config\config;
use phpbb\controller\helper as controller;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\template;

abstract class plugin_base
{
	public readonly bool $loadable;

	public function __construct(
		protected config $config,
		protected controller $controller,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected template $template,
		protected users_loader $users_loader,
		protected readonly string $root_path,
		protected readonly string $php_ext
	)
	{
	}

	/**
	* @param string $set Is plugin loadable
	*/
	public function is_loadable(bool $set): void
	{
		$this->loadable = $set;
	}

	public function load_plugin(): void
	{
	}

	/**
	* Truncate title
	*/
	public function truncate(string $title, int $length, ?string $ellips = null): string
	{
		return truncate_string(censor_text($title), $length, 255, false, $ellips ?? '...');
	}
}
