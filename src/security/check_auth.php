<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\security;

use phpbb\auth\auth;

class check_auth
{
	public array $limit_access = [];
	protected static array $roles = ['ROLE_ADMIN', 'USER'];

	public function __construct(public readonly auth $auth)
	{
	}

	public function is_granted(object $data): bool
	{
		if (!in_array($data->role, self::$roles))
		{
			return false;
		}

		return str_contains($data->option, ',')
			? $this->auth->acl_gets($data->option)
			: $this->auth->acl_get($data->option);
	}
}
