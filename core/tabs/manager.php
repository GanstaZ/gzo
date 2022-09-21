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
		$this->register_tab_types($collection);
	}

	/**
	* Register all available tabs
	*
	* @param Service collection of tabs
	*/
	protected function register_tab_types($collection): void
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
	public function available(): array
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

	/**
	* Generate menu for tabs
	*
	* @param string $username
	* @param object $controller
	* @param object $template
	* @return void
	*/
	public function generate_tabs_menu(string $username, object $controller, object $template): void
	{
		foreach ($this->available() as $tab)
		{
			$route = $controller->route('ganstaz_web_member_tab', ['username' => $username, 'tab' => $tab]);
			if ($tab === 'profile')
			{
				$route = $controller->route('ganstaz_web_member', ['username' => $username]);
			}

			$template->assign_block_vars('tabs', [
				'title' => ucfirst($tab),
				'link' => $route,
			]);
		}
	}

	/**
	* Generate breadcrumb for tabs
	*
	* @param string $username
	* @param object $controller
	* @param object $language
	* @param object $template
	* @param string $tab
	* @return void
	*/
	public function generate_tabs_breadcrumb(string $username, object $controller, object $language, object $template, string $tab): void
	{
		$template->assign_block_vars('navlinks', [
			'BREADCRUMB_NAME'	=> $language->lang('MEMBERLIST'),
			// TODO: Add route for members controller
			'U_BREADCRUMB'		=> 'test', //append_sid("{$this->root_path}memberlist.$this->php_ext"),
		]);
		$template->assign_block_vars('navlinks', [
			'BREADCRUMB_NAME'	=> $username,
			'U_BREADCRUMB'		=> $controller->route('ganstaz_web_member', ['username' => $username]),
		]);

		if ($tab !== 'profile')
		{
			$template->assign_block_vars('navlinks', [
				'BREADCRUMB_NAME'	=> ucfirst($tab),
				'U_BREADCRUMB'		=> $controller->route('ganstaz_web_member_tab', ['username' => $username, 'tab' => $tab]),
			]);
		}
	}
}
