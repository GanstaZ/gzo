<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\auth;

use ganstaz\gzo\src\attribute\auth as auth_attribute;
use Symfony\Component\DependencyInjection\ContainerInterface as container;
use phpbb\auth\auth as phpbb_auth;
use phpbb\exception\http_exception;

class auth
{
	public static array $roles = [];

	public function __construct(protected container $container, public readonly phpbb_auth $phpbb_auth)
	{
	}

	public function authorize(object|string $controller): void
	{
		if (is_string($controller))
		{
			[$service, $current_method] = explode(':', $controller);

			$controller = $this->container->get($service);
		}

		$reflection = new \ReflectionClass($controller);
		$attributes = $reflection->getAttributes(auth_attribute::class, \ReflectionAttribute::IS_INSTANCEOF);

		$this->target_class($attributes);

		if (!$attributes && isset($current_method))
		{
			$this->target_method($reflection->getMethod($current_method));
		}
	}

	protected function target_class($attributes): void
	{
		if (!$attributes)
		{
			return;
		}

		foreach ($attributes as $attribute)
		{
			$data = $attribute->newInstance();
			$this->auth_access($data);

			// Testing
			// var_dump($data);
		}
	}

	protected function target_method(object $method): void
	{
		foreach ($method->getAttributes(auth_attribute::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute)
		{
			$data = $attribute->newInstance();
			$this->auth_access($data);

			// Testing
			// var_dump($method->getName());
			// var_dump($data);
		}
	}

	protected function auth_access(object $data): mixed
	{
		return match($data->role)
		{
			'ROLE_USER'	 => $this->is_granted($data),
			'ROLE_ADMIN' => $this->is_granted($data),
			default		 => $this->set_roles($data)
		};
	}

	protected function set_roles($data): void
	{
		var_dump('special_test');

		if (!$this->phpbb_auth->acl_get($data->option) && !isset(self::$roles[$data->role]))
		{
			self::$roles[$data->role] = $data->option;
		}
	}

	protected function is_granted(object $data): bool
	{
		$auth = str_contains($data->option, ',')
			? $this->phpbb_auth->acl_gets($data->option)
			: $this->phpbb_auth->acl_get($data->option);

		if (!$auth)
		{
			throw new http_exception($data->code, $data->message);
		}

		return true;
	}
}
