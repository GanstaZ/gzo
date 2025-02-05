<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\security\event;

use ganstaz\gzo\src\area\loader;
use ganstaz\gzo\src\enum\admin;
use ganstaz\gzo\src\security\attribute\auth;
use ganstaz\gzo\src\security\check_auth;
use phpbb\exception\http_exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class auth_attribute implements EventSubscriberInterface
{
	public function __construct(protected check_auth $check_auth, protected loader $loader)
	{
	}

	/**
	 * Borrowed from the Symfony IsGrantedAttributeListener
	 *
	 * @author Ryan Weaver <ryan@knpuniversity.com>
	 */
	public function on_kernel_controller_arguments(ControllerArgumentsEvent $event): void
	{
		if (!\is_array($attributes = $event->getAttributes()[auth::class] ?? null))
		{
			return;
		}

		foreach ($attributes as $attribute)
		{
			var_dump($attribute);

			if (!$this->check_auth->is_granted($attribute))
			{
				throw new http_exception($attribute->status_code, $attribute->message);
			}
		}

		$route = $event->getRequest()->attributes->get('_route');
		$type = strstr($route, '_', true);

		if ($this->loader->available($type))
		{
			$area = $this->loader->get($type);

			define(admin::GZO_IN_AREA, true);

			$area->build_navigation_data($this->check_auth->auth)
				->load_navigation();
		}
	}

	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::CONTROLLER_ARGUMENTS => ['on_kernel_controller_arguments', 20],
		];
	}
}
