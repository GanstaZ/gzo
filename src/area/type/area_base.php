<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\area\type;

use phpbb\cache\service as cache;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use ganstaz\gzo\src\event\events;
use ganstaz\gzo\src\controller\helper;

abstract class area_base
{
	protected bool|array $navigation = [];
	protected array $categories = ['GZO_DEFAULT' => 'home',];

	public function __construct(
		protected cache $cache,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected helper $helper,
		protected readonly string $table
	)
	{
	}

	abstract public function get_name(): string;

	abstract public function load_navigation(string $type, string $route): void;

	protected function build_navigation(string $type, string $breadcrumb_name, string $breadcrumb_route): void
	{
		$this->helper->assign_breadcrumb($breadcrumb_name, $breadcrumb_route);

		$categories = $this->categories;
		// $navigation = $this->navigation[$type];
		$navigation = $this->get_navigation_data($type);

		/** @event events::GZO_AREA_MODIFY_NAVIGATION */
		$vars = ['categories', 'navigation', 'type'];
		extract($this->dispatcher->trigger_event(events::GZO_AREA_MODIFY_NAVIGATION, compact($vars)));

		$this->categories = $categories;
		unset($categories);

		foreach ($navigation as $category => $data)
		{
			$this->helper->twig->assign_block_vars('menu', [
				'heading' => $category,
				'icon'	  => $this->categories[$category] ?? $this->categories['GZO_DEFAULT'],
			]);

			foreach ($data as $item)
			{
				$this->helper->twig->assign_block_vars('menu.item', [
					'title' => $item['title'],
					'route' => $item['route'],
					'icon'	=> $item['icon'] ?? '',
				]);
			}
		}
	}

	public function navigation_data(?string $type = null, ?object $auth = null): self
	{
		if (($this->navigation = $this->cache->get('_gzo_plugins_test')) === false)
		{
			$sql = 'SELECT *
					FROM ' . $this->table . 'gzo_plugins
					ORDER BY id';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->navigation[$row['type']][$row['cat']][] = $row;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_gzo_plugins_test', $this->navigation);
		}

		foreach ($this->navigation[$type] as $cat => $data)
		{
			var_dump($cat);

			foreach ($data as $key => $item)
			{
				var_dump($key);

				// Not allowed to view plugin controller?
				// unset($this->navigation[$type][$cat][$key]);
				// var_dump($item['route']);
				if (!$auth->acl_get($item['auth']))
				{}
			}
		}

		return $this;
	}

	public function get_navigation_data(?string $type = null): array
	{
		if (($navigation = $this->cache->get('_gzo_plugins')) === false)
		{
			$sql = 'SELECT *
					FROM ' . $this->table . 'gzo_plugins
					ORDER BY id';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$navigation[$row['type']][$row['cat']][] = [
					'title' => $row['title'],
					'route' => $row['route'],
					'icon'	=> $row['icon'] ?? '',
			   ];
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_gzo_plugins', $navigation);
		}

		return $navigation[$type] ?? $navigation;
	}

	protected function set_category_icon(string $name, string $icon): self
	{
		if (!isset($this->categories[$name]))
		{
			$this->categories[$name] = $icon;
		}

		return $this;
	}
}
