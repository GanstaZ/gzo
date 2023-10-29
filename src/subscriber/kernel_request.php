<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\subscriber;

use ganstaz\gzo\src\area\loader;
use ganstaz\gzo\src\enum\admin;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class kernel_request implements EventSubscriberInterface
{
	public function __construct(private loader $loader)
	{
	}

	/**
	* KernelEvents::REQUEST
	*/
	public function on_kernel_request(GetResponseEvent $event): void
	{
		$route = $event->getRequest()->attributes->get('_route');
		$name = strstr($route, '_', true);

		if ($this->loader->is_area_available($name))
		{
			define(admin::GZO_IN_AREA, true);

			$area = $this->loader->get_area($name);
			$area->load();
		}
	}

	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::REQUEST => 'on_kernel_request',
		];
	}
}
