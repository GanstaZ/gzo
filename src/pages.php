<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src;

use phpbb\cache\service as cache;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\user;

/**
* Pages class
*/
class pages
{
	/** @var cache */
	protected $cache;

	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var user */
	protected $user;

	/** @var page table */
	protected $page_data;

	/** @var php file extension */
	protected $php_ext;

	/** @var array allow */
	protected $allow = [];

	private bool $is_page_admin = false;

	private bool $is_pagename_app = false;

	/**
	* Constructor
	*
	* @param cache			  $cache	 Cache object
	* @param config			  $config	 Config object
	* @param driver_interface $db		 Database object
	* @param user			  $user		 User object
	* @param string			  $page_data The name of the page data table
	* @param string			  $php_ext	 PHP file extension
	*/
	public function __construct(cache $cache, config $config, driver_interface $db, user $user, $page_data, $php_ext)
	{
		$this->cache	 = $cache;
		$this->config	 = $config;
		$this->db		 = $db;
		$this->user		 = $user;
		$this->page_data = $page_data;
		$this->php_ext	 = $php_ext;
	}

	public function is_pagename_app(): bool
	{
		return $this->is_pagename_app;
	}

	public function is_page_admin(): bool
	{
		return $this->is_page_admin;
	}

	/**
	* Get current page
	*
	* @return string
	*/
	public function get_current_page(): string
	{
		return substr($this->user->page['page_name'], 0, strpos($this->user->page['page_name'], '.'));
	}

	/**
	* Get page data for blocks loader
	*
	* @return array
	*/
	public function get_page_data(): array
	{
		$on_page = explode('/', str_replace('.' . $this->php_ext, '', $this->user->page['page_name']));
		$page_name = $on_page[0];

		// Do we have any special pages, that can load everything (route paths)
		$this->check_allowed_condition();

		if ($page_name === 'app')
		{
			$this->is_pagename_app = true;

			if (isset($on_page[1]) && $on_page[1] === 'admin')
			{
				$this->is_page_admin = true;
			}

			$get_last  = end($on_page);
			$page_name = count($on_page) > 2 && is_numeric($get_last) ? $on_page[1] : $get_last;
			$page_name = isset($on_page[1]) && $this->allow($on_page[1]) ? $on_page[1] : $page_name;
		}

		// This is global for app.php & will load everything, even, if above route path/s is/are disabled.
		$page_name = $this->allow($on_page[0]) ? $on_page[0] : $page_name;

		return !$this->is_cp($page_name) ? $this->get($page_name) : [];
	}

	/**
	* Check, if we are in cps or not
	*
	* @param string $page_name Current page name
	* @return bool
	*/
	public function is_cp(string $page_name): bool
	{
		return $this->user->page['page_dir'] === 'adm' || $page_name === 'mcp' || $page_name === 'ucp';
	}

	/**
	* Check, if page is in allowed array or not
	*
	* @param string $name Current page name
	* @return bool
	*/
	protected function allow(string $name): bool
	{
		return isset($this->allow[$name]) || array_key_exists($name, $this->allow);
	}

	/**
	* Check, if page is special/allowed
	*
	* @return void
	*/
	protected function check_allowed_condition(): void
	{
		$sql = 'SELECT name, allow
				FROM ' . $this->page_data . '
				WHERE active = 1
				ORDER BY id';
		$result = $this->db->sql_query($sql, 300);

		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['allow'])
			{
				$this->allow[$row['name']] = $row['allow'];
			}
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Get page data
	*
	* @param string $name Page name
	* @return array
	*/
	public function get(string $name): array
	{
		$enabled = [];
		foreach (['gzo_right', 'gzo_left', 'gzo_middle', 'gzo_top', 'gzo_bottom'] as $section)
		{
			if ($this->config[$section])
			{
				$enabled[] = $section;
			}
		}

		$enabled = implode($this->user->lang['COMMA_SEPARATOR'], $enabled);

		if (($pages = $this->cache->get('_gzo_pages')) === false)
		{
			$sql = 'SELECT name, ' . $enabled . '
					FROM ' . $this->page_data . '
					WHERE active = 1
					ORDER BY id';
			$result = $this->db->sql_query($sql);

			$pages = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				// Filter out inactive sections & unset page name, as we don't need it in data array
				$data = array_keys(array_filter($row));
				unset($data[0]);

				$pages[$row['name']] = $data;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_gzo_pages', $pages);
		}

		return $pages[$name] ?? [];
	}
}
