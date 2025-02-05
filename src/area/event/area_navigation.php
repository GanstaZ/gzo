<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\area\event;

use ganstaz\gzo\src\area\loader;
use ganstaz\gzo\src\enum\admin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class area_navigation implements EventSubscriberInterface
{
	public function __construct(protected loader $loader)
	{
	}

	public function on_kernel_controller_arguments(ControllerArgumentsEvent $event): void
	{
		$route = $event->getRequest()->attributes->get('_route');
		$type = strstr($route, '_', true);

		if ($this->loader->available($type))
		{
			$area = $this->loader->get($type);

			define(admin::GZO_IN_AREA, true);

			$area->build_navigation_data()
				->load_navigation();
		}
		var_dump('Navigation');
	}

	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::CONTROLLER_ARGUMENTS => 'on_kernel_controller_arguments',
		];
	}
}
