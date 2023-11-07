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
	public array $limit_access = [];
	protected static array $roles = ['ADMIN', 'USER'];

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
		if ($attributes)
		{
			foreach ($attributes as $attribute)
			{
				$data = $attribute->newInstance();
				$this->auth_access($data);

				// Testing
				// var_dump($data);
			}
		}
	}

	protected function target_method(object $method): void
	{
		foreach ($method->getAttributes(auth_attribute::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute)
		{
			$data = $attribute->newInstance();
			$this->auth_access($data);

			// Testing
			var_dump($method->getName());
			var_dump($data);
		}
	}

	protected function auth_access(object $data): mixed
	{
		$auth = str_contains($data->option, ',')
			? $this->phpbb_auth->acl_gets($data->option)
			: $this->phpbb_auth->acl_get($data->option);

		if (in_array($data->role, self::$roles))
		{
			return $this->is_granted($auth, $data);
		}

		return $this->limit_access($auth, $data);
	}

	protected function limit_access(bool $auth, object $data): void
	{
		if (!$auth && !isset($this->limit_access[$data->role]))
		{
			$this->limit_access[$data->role] = $data->option;
		}
	}

	protected function is_granted(bool $auth, object $data): bool
	{
		if (!$auth)
		{
			throw new http_exception($data->status_code, $data->message);
		}

		return true;
	}
}
