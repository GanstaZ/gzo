<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\tabs;

use phpbb\di\service_collection;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\template\twig\twig;

/**
* Tabs manager
*/
class manager
{
	protected static array $tabs = [];

	public function __construct(
		private service_collection $collection,
		private controller $controller,
		private language $language,
		private twig $twig,
		private readonly string $root_path,
		private readonly string $php_ext
	)
	{
		if ($collection)
		{
			foreach ($collection as $tab)
			{
				self::$tabs[$tab->get_name()] = $tab;
			}
		}
	}

	/**
	* Get tab type by name
	*/
	public function get(string $name): object
	{
		return self::$tabs[$name] ?? (object) [];
	}

	/**
	* Get all available tabs
	*/
	public function available(): array
	{
		return array_keys(self::$tabs) ?? [];
	}

	/**
	* Remove tab
	*/
	public function remove(string $name): void
	{
		if (isset(self::$tabs[$name]) || array_key_exists($name, self::$tabs))
		{
			unset(self::$tabs[$name]);
		}
	}

	/**
	* Generate menu for tabs
	*/
	public function generate_tabs_menu(string $username, string $tab): void
	{
		if (count($this->available()) === 1)
		{
			return;
		}

		foreach ($this->available() as $tab)
		{
			$route = $this->controller->route('ganstaz_gzo_member_tab', ['username' => $username, 'tab' => $tab]);
			if ($tab === 'profile')
			{
				$route = $this->controller->route('ganstaz_gzo_member', ['username' => $username]);
			}

			$this->twig->assign_block_vars('tabs', [
				'title' => $this->language->lang('GZO_' . strtoupper($tab)),
				'link' => $route,
				'icon' => $this->get($tab)->icon(),
			]);
		}
	}

	/**
	* Generate breadcrumb for tabs
	*
	* @deprecated 2.4.0-a30
	*/
	public function generate_tabs_breadcrumb(string $username, string $tab): void
	{
		$this->twig->assign_block_vars_array('navlinks', [
			[
				'BREADCRUMB_NAME'	=> $this->language->lang('MEMBERLIST'),
				// TODO: Add route for members controller
				'U_BREADCRUMB'		=> append_sid("{$this->root_path}memberlist.$this->php_ext"),
			],
			[
				'BREADCRUMB_NAME'	=> $username,
				'U_BREADCRUMB'		=> $this->controller->route('ganstaz_gzo_member', ['username' => $username]),
			],
		]);

		if ($tab !== 'profile')
		{
			$this->twig->assign_block_vars('navlinks', [
				'BREADCRUMB_NAME'	=> ucfirst($tab),
				'U_BREADCRUMB'		=> $this->controller->route('ganstaz_gzo_member_tab', ['username' => $username, 'tab' => $tab]),
			]);
		}
	}
}
