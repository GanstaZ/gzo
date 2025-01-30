<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\plugin\blocks;

use ganstaz\gzo\src\plugin\base as plugin_base;
use ganstaz\gzo\src\user\loader as users_loader;
use phpbb\config\config;
use phpbb\controller\helper as controller;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\template;

abstract class base extends plugin_base implements block_interface
{
	protected readonly bool $loading;

	public function __construct(
		config $config,
		controller $controller,
		driver_interface $db,
		dispatcher $dispatcher,
		template $template,
		users_loader $users_loader,
		$root_path,
		$php_ext
	)
	{
		parent::__construct($config, $controller, $db, $dispatcher, $template, $users_loader, $root_path, $php_ext);
	}

	/**
	* {@inheritdoc}
	*/
	public function set_active(bool $set): void
	{
		$this->loading = $set;
	}

	/**
	* {@inheritdoc}
	*/
	public function is_load_active(): bool
	{
		return $this->loading;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_block_data(): array
	{
		return [];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
	}
}
