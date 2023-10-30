<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\blocks\type;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\controller\helper as controller;
use phpbb\template\twig\twig;
use phpbb\event\dispatcher;
use ganstaz\gzo\src\helper;

abstract class base implements block_interface
{
	protected bool $loading;

	public function __construct(
		protected config $config,
		protected driver_interface $db,
		protected controller $controller,
		protected twig $twig,
		protected dispatcher $dispatcher,
		protected helper $helper,
		protected readonly string $root_path,
		protected readonly string $php_ext
	)
	{
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
