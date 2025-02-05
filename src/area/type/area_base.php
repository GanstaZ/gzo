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

use ganstaz\gzo\src\helper\controller_helper;
use ganstaz\gzo\src\enum\gzo;
use ganstaz\gzo\src\event\events;
use phpbb\cache\service as cache;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\template;

abstract class area_base
{
	public readonly string $type;

	protected bool|array $navigation = [];
	protected array $icons = ['GZO_DEFAULT' => 'ic--outline-home'];

	public function __construct(
		protected cache $cache,
		protected driver_interface $db,
		protected dispatcher $dispatcher,
		protected template $template,
		protected controller_helper $controller_helper,
		protected readonly string $table
	)
	{
	}

	/**
	* @param string $type Area type
	*/
	public function set_type(string $type)
	{
		$this->type = $type;
	}

	abstract public function load_navigation(): void;

	public function build_navigation_data(?object $auth): self
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

		foreach ($this->navigation[$this->type] as $cat => $data)
		{
			$this->filter_navigation_data($cat, $data, $auth);
		}

		return $this;
	}

	protected function filter_navigation_data(string $cat, array $data, ?object $auth): void
	{
		foreach ($data as $key => $row)
		{
			if ($row['parent'])
			{
				$this->set_category_icon($row['cat'], $row['icon']);
				unset($this->navigation[$this->type][$cat][$key]);
			}

			// Unset Area controller if user doesn't have permissions to view it
			if ($row['auth'] && !$auth->acl_get($row['auth']))
			{
				unset($this->navigation[$this->type][$cat][$key]);
			}
		}
	}

	protected function create_view(string $breadcrumb_name, string $breadcrumb_route): void
	{
		$this->controller_helper->assign_breadcrumb($breadcrumb_name, $breadcrumb_route);

		$icons = $this->icons;
		$type = $this->type;
		$navigation = $this->navigation[$type];

		/** @event events::GZO_AREA_MODIFY_NAVIGATION */
		$vars = ['icons', 'navigation', 'type'];
		extract($this->dispatcher->trigger_event(events::GZO_AREA_MODIFY_NAVIGATION, compact($vars)));

		$this->icons = $icons;
		$this->navigation = $navigation;
		unset($navigation, $icons, $type);

		foreach ($this->navigation as $category => $data)
		{
			$this->template->assign_block_vars('menu', [
				'heading' => $category,
				'icon'	  => $this->icons[$category] ?? $this->icons['GZO_DEFAULT'],
			]);

			foreach ($data as $item)
			{
				$this->template->assign_block_vars('menu.item', [
					'title' => $item['title'],
					'route' => $item['route'],
					'icon'	=> $item['icon'] ?? ''
				]);
			}
		}
	}

	protected function set_category_icon(string $name, string $icon): self
	{
		if ($icon && !isset($this->icons[$name]))
		{
			$this->icons[$name] = $icon;
		}

		return $this;
	}
}
