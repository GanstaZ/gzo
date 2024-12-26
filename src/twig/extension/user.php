<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\twig\extension;

use phpbb\auth\auth;
use phpbb\template\twig\environment;
use phpbb\user as phpbb_user;
use Twig\Error\Error;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class user extends AbstractExtension
{
	public function __construct(
		protected auth $auth,
		protected phpbb_user $phpbb_user
	)
	{
	}

	/**
	* Returns a list of functions to add to the existing list.
	*
	* @return TwigFunction[]			Array of twig functions
	*/
	public function getFunctions(): array
	{
		return [
			new TwigFunction('gzo_username', [$this, 'username'], ['needs_environment' => true]),
		];
	}

	public function username(environment $environment, string $mode, string $user_id, string $username, string $color, string $classes = ''): string
	{
		$s_granted = false;
		if ($user_id && $user_id != ANONYMOUS && ($this->phpbb_user->data['user_id'] == ANONYMOUS || $this->auth->acl_get('u_viewprofile')))
		{
			$s_granted = true;
		}

		try
		{
			return $environment->render('@ganstaz_gzo/macros/user/username.twig', [
				'MODE'		=> $mode,
				'USERNAME'	=> $username,
				'COLOR'		=> $color,
				'CLASSES'	=> $classes,
				'S_GRANTED' => (bool) $s_granted
			]);
		}
		catch (Error $e)
		{
			return '';
		}
	}
}
