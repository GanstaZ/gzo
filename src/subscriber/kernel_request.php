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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class kernel_request implements EventSubscriberInterface
{
	public function __construct(protected auth $auth, protected loader $loader)
	{
	}

	/**
	* KernelEvents::REQUEST
	*/
	public function on_kernel_request(RequestEvent $event): void
	{
		$route = $event->getRequest()->attributes->get('_route');
		$type = strstr($route, '_', true);

		if ($this->loader->available($type))
		{
			$area = $this->loader->get($type);
			$this->auth->authorize($area);

			define(admin::GZO_IN_AREA, true);

			$area->build_navigation_data($this->auth->phpbb_auth)
				->load_navigation();
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
