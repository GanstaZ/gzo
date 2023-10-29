<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\area;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use ganstaz\gzo\src\event\events;
use ganstaz\gzo\src\controller\helper;

/**
* Base class for areas
*/
abstract class area_base
{
	protected array $categories = ['GZO_DEFAULT' => 'home',];

	public function __construct(
		protected auth $auth,
		protected readonly driver_interface $db,
		protected dispatcher $dispatcher,
		protected helper $helper,
		private readonly string $table
	) {
	}

	abstract public function get_name(): string;

	abstract public function load(): void;

	protected function build_navigation(string $type, string $breadcrumb_name, string $breadcrumb_route): void
	{
		$this->helper->assign_breadcrumb($breadcrumb_name, $breadcrumb_route);

		$navigation = $this->get_navigation_data($type);
		$categories = $this->categories;

		// var_dump($navigation);

		/** @event events::GZO_AREA_MODIFY_NAVIGATION */
		$vars = ['navigation', 'categories'];
		extract($this->dispatcher->trigger_event(events::GZO_AREA_MODIFY_NAVIGATION, compact($vars)));

		$this->categories = $categories;
		unset($categories);

		var_dump($this->categories);

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

	protected function get_navigation_data(?string $type = null): array
	{
		$sql = 'SELECT *
			FROM ' . $this->table . 'gzo_plugins
			ORDER BY id';
		$result = $this->db->sql_query($sql);

		$navigation = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$navigation[(string) $row['type']][(string)$row['cat']][] = [
				'title' => (string) $row['title'],
				'route' => (string) $row['route'],
				'icon'	=> (string) $row['icon'] ?? '',
		   ];
		}
		$this->db->sql_freeresult($result);

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
