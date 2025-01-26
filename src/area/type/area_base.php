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

use ganstaz\gzo\src\controller\helper;
use ganstaz\gzo\src\enum\gzo;
use ganstaz\gzo\src\event\events;
use phpbb\cache\service as cache;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;

abstract class area_base
{
	protected bool|array $navigation = [];
	protected array $icons = ['GZO_DEFAULT' => 'ic--outline-home'];

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

	abstract public function load_navigation(string $type): void;

	public function navigation_data(string $type, ?object $auth): self
	{
		if (($this->navigation = $this->cache->get('_gzo_area')) === false)
		{
			$sql = 'SELECT *
					FROM ' . $this->table . gzo::AREA . '
					ORDER BY id';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->navigation[$row['type']][$row['cat']][] = $row;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_gzo_area', $this->navigation);
		}

		foreach ($this->navigation[$type] as $cat => $data)
		{
			$this->filter_navigation_data($type, $cat, $data, $auth);
		}

		return $this;
	}

	protected function filter_navigation_data(string $type, string $cat, array $data, $auth): void
	{
		foreach ($data as $key => $item)
		{
			$item_auth = isset($item['auth']) && $item['auth'];

			// Not allowed to view plugin controller?
			if ($item_auth && !$auth->acl_get($item['auth']))
			{
				unset($this->navigation[$type][$cat][$key]);
			}
		}
	}

	protected function build_navigation(string $type, string $breadcrumb_name, string $breadcrumb_route): void
	{
		$this->helper->assign_breadcrumb($breadcrumb_name, $breadcrumb_route);

		$icons = $this->icons;
		$navigation = $this->navigation[$type];

		/** @event events::GZO_AREA_MODIFY_NAVIGATION */
		$vars = ['icons', 'navigation', 'type'];
		extract($this->dispatcher->trigger_event(events::GZO_AREA_MODIFY_NAVIGATION, compact($vars)));

		$this->icons = $icons;
		$this->navigation = $navigation;
		unset($navigation, $icons);

		foreach ($this->navigation as $category => $data)
		{
			$this->helper->twig->assign_block_vars('menu', [
				'heading' => $category,
				'icon'	  => $this->icons[$category] ?? $this->icons['GZO_DEFAULT'],
			]);

			foreach ($data as $item)
			{
				$this->helper->twig->assign_block_vars('menu.item', [
					'title' => $item['title'],
					'route' => $item['route'],
					'icon'  => $item['icon'] ?? ''
				]);
			}
		}
	}

	protected function set_category_icon(string $name, string $icon): self
	{
		if (!isset($this->icons[$name]))
		{
			$this->icons[$name] = $icon;
		}

		return $this;
	}
}
