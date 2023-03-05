<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\tabs\type;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\user;
use phpbb\exception\http_exception;

/**
* GZO Web: base class for tabs
*/
abstract class base implements tabs_interface
{
	/** @var auth */
	protected $auth;

	/** @var driver_interface */
	protected $db;

	/** @var dispatcher */
	protected $dispatcher;

	/** @var controller helper */
	protected $controller;

	/** @var language */
	protected $language;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var string name */
	protected $name;

	/** @var bool active session */
	protected $active_session = false;

	/**
	* Constructor
	*
	* @param auth			  $auth		  Auth object
	* @param driver_interface $db		  Database object
	* @param dispatcher		  $dispatcher Dispatcher object
	* @param controller		  $controller Controller helper object
	* @param language		  $language	  Language object
	* @param template		  $template	  Template object
	* @param user			  $user		  User object
	*/
	public function __construct
	(
		auth $auth,
		driver_interface $db,
		dispatcher $dispatcher,
		controller $controller,
		language $language,
		template $template,
		user $user
	)
	{
		$this->auth		  = $auth;
		$this->db		  = $db;
		$this->dispatcher = $dispatcher;
		$this->controller = $controller;
		$this->language	  = $language;
		$this->template	  = $template;
		$this->user		  = $user;
	}

	/**
	* Returns the namespace
	*
	* @return string Twig namespace
	*/
	abstract protected function namespace();

	/**
	* Load current user
	*
	* @param string $username Name of the member
	* @return void
	*/
	abstract protected function load(string $username);

	/**
	* {@inheritdoc}
	*/
	public function set_name(string $name): void
	{
		$this->name = $name;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_name(): string
	{
		return $this->name;
	}

	/**
	* {@inheritdoc}
	*/
	public function icon(): string
	{
		return 'bug';
	}

	/**
	* Get user data
	*
	* @param string $username
	* @return array
	*/
	public function get_user_data(string $username): array
	{
		// Can this user view profiles/memberlist?
		if (!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				throw new http_exception(403, 'NO_VIEW_USERS');
			}

			login_box('', $this->language->lang('LOGIN_EXPLAIN_VIEWPROFILE'));
		}

		$sql_array = [
			'SELECT'	=> 'u.*',
			'FROM'		=> [
				USERS_TABLE		=> 'u'
			],
			'WHERE'		=> "u.username_clean = '" . $this->db->sql_escape(utf8_clean_string($username)) . "'",
		];

		/**
		* Modify user data SQL before member profile row is created
		*
		* @event core.memberlist_modify_viewprofile_sql
		* @var string	username			The username
		* @var array	sql_array			Array containing the main query
		* @since 3.2.6-RC1
		*/
		$vars = [
			'username',
			'sql_array',
		];
		extract($this->dispatcher->trigger_event('core.memberlist_modify_viewprofile_sql', compact($vars)));

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$member = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$member)
		{
			throw new http_exception(404, 'NO_USER');
		}

		$this->active_session = $this->user->data['username'] === $member['username'];

		return $member;
	}

	/**
	* Is active session
	*
	* @return bool
	*/
	public function is_active_session(): bool
	{
		return $this->active_session;
	}
}
