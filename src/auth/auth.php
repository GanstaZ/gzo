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

		$this->class_attributes($attributes);

		if (!$attributes && isset($current_method))
		{
			$this->method_attributes($reflection->getMethod($current_method));
		}
	}

	protected function class_attributes($attributes): void
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
			var_dump($data);
		}
	}

	protected function method_attributes($method): void
	{
		foreach($method->getAttributes(auth_attribute::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute)
		{
			$data = $attribute->newInstance();
			$this->auth_access($data);

			// Testing
			var_dump($method->getName());
			var_dump($data);
		}
	}

	protected function auth_access($data): mixed
	{
		return match($data->role)
		{
			'ROLE_USER'  => $this->user_area($data),
			'ROLE_ADMIN' => $this->admin_area($data),
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

	// TODO: To be removed
	protected function admin_area($data)
	{
		var_dump('admin_test');

		$this->is_granted($data->option, $data->message, $data->status_code);
	}

	// TODO: To be removed
	protected function user_area($data)
	{
		var_dump('user_test');

		$this->is_granted($data->option, $data->message, $data->status_code);
	}

	protected function is_granted(string $option, string $message, int $code): bool
	{
		if (!$this->phpbb_auth->acl_get($option))
		{
			throw new http_exception($code, $message);
		}

		return true;
	}
}
