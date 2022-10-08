<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\tabs;

use phpbb\di\service_collection;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\template\template;

/**
* GZO Web: tabs manager
*/
class manager
{
	/** @var controller helper */
	protected $controller;

	/** @var language */
	protected $language;

	/** @var template */
	protected $template;

	/** @var array Contains all available tabs */
	protected static $tabs = false;

	/** @var root_path */
	protected $root_path;

	/** @var php_ext */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param service_collection $collection Our tabs
	* @param controller         $controller Controller helper object
	* @param language           $language   Language object
	* @param template           $template   Template object
	* @param string	            $root_path  Path to the phpbb includes directory
	* @param string	            $php_ext    PHP file extension
	*/
	public function __construct(service_collection $collection, controller $controller, language $language, template $template, $root_path, $php_ext)
	{
		$this->register_tab_types($collection);

		$this->controller = $controller;
		$this->language   = $language;
		$this->template   = $template;
		$this->root_path  = $root_path;
		$this->php_ext    = $php_ext;
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
	* @param string $tab
	* @return void
	*/
	public function generate_tabs_menu(string $username, string $tab): void
	{
		foreach ($this->available() as $tab)
		{
			$route = $this->controller->route('ganstaz_web_member_tab', ['username' => $username, 'tab' => $tab]);
			if ($tab === 'profile')
			{
				$route = $this->controller->route('ganstaz_web_member', ['username' => $username]);
			}

			$this->template->assign_block_vars('tabs', [
				'title' => $this->language->lang('GZO_' . strtoupper($tab)),
				'link' => $route,
			]);
		}
	}

	/**
	* Generate breadcrumb for tabs
	*
	* @param string $username
	* @param string $tab
	* @return void
	*/
	public function generate_tabs_breadcrumb(string $username, string $tab): void
	{
		$this->template->assign_block_vars_array('navlinks', [
			[
				'BREADCRUMB_NAME'	=> $this->language->lang('MEMBERLIST'),
				// TODO: Add route for members controller
				'U_BREADCRUMB'		=> append_sid("{$this->root_path}memberlist.$this->php_ext"),
			],
			[
				'BREADCRUMB_NAME'	=> $username,
				'U_BREADCRUMB'		=> $this->controller->route('ganstaz_web_member', ['username' => $username]),
			],
		]);

		if ($tab !== 'profile')
		{
			$this->template->assign_block_vars('navlinks', [
				'BREADCRUMB_NAME'	=> ucfirst($tab),
				'U_BREADCRUMB'		=> $this->controller->route('ganstaz_web_member_tab', ['username' => $username, 'tab' => $tab]),
			]);
		}
	}
}
