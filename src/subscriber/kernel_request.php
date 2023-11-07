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
use ganstaz\gzo\src\auth\auth;
use ganstaz\gzo\src\enum\admin;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class kernel_request implements EventSubscriberInterface
{
	public function __construct(private auth $auth, private loader $loader)
	{
	}

	/**
	* KernelEvents::REQUEST
	*/
	public function on_kernel_request(GetResponseEvent $event): void
	{
		$route = $event->getRequest()->attributes->get('_route');
		$type = strstr($route, '_', true);

		if ($this->loader->is_area_available($type))
		{
			$area = $this->loader->get_area($type);
			$this->auth->authorize($area);

			define(admin::GZO_IN_AREA, true);

			$area->navigation_data($type, $this->auth->phpbb_auth)
				->load_navigation($type, $route);
		}

		$this->auth->authorize($event->getRequest()->attributes->get('_controller'));
	}

	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::REQUEST => 'on_kernel_request',
		];
	}
}
