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
use phpbb\event\dispatcher;
use ganstaz\gzo\src\event\events;
use ganstaz\gzo\src\controller\helper;
use ganstaz\gzo\src\contract\area_navigation;

/**
* Base class for areas
*/
abstract class area_base
{
	public function __construct(
		protected auth $auth,
		protected dispatcher $dispatcher,
		protected helper $helper
	)
	{
	}

	abstract protected function get_name(): string;

	abstract protected function load(): void;

	protected function build_navigation(area_navigation $area_navigation, string $breadcrumb_name, string $breadcrumb_route): void
	{
		$this->helper->assign_breadcrumb($breadcrumb_name, $breadcrumb_route);

		$navigation = [];
		foreach ($area_navigation::items() as $item)
		{
			$cat = $item['cat'] ?? $area_navigation::GZO_CAT;

			$navigation[$cat][] = [
				'title' => $item['title'],
				'route' => $item['route'],
				'icon'	=> $item['icon'],
			];
		}

		/** @event ganstaz.gzo.area_modify_navigation */
		$vars = ['navigation'];
		extract($this->dispatcher->trigger_event(events::GZO_AREA_MODIFY_NAVIGATION, compact($vars)));

		foreach ($navigation as $category => $data)
		{
			$this->helper->twig->assign_block_vars('menu', [
				'heading' => $category,
				'icon'	  => $area_navigation::GZO_ICON[$category] ?? $area_navigation::GZO_ICON,
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
		unset($navigation);
	}
}
