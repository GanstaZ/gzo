<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\template;
use phpbb\user;

/**
* GZO Web: forum info helper class
*/
class info
{
	/** @var auth */
	protected $auth;

	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var dispatcher */
	protected $dispatcher;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var root_path */
	protected $root_path;

	/** @var php_ext */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param auth             $auth       Auth object
	* @param config			  $config	  Config object
	* @param driver_interface $db		  Database object
	* @param dispatcher		  $dispatcher Dispatcher object
	* @param template         $template   Template object
	* @param user             $user       User object
	* @param string			  $root_path  Path to the phpbb includes directory
	* @param string			  $php_ext	  PHP file extension
	*/
	public function __construct
	(
		auth $auth,
		config $config,
		driver_interface $db,
		dispatcher $dispatcher,
		template $template,
		user $user,
		$root_path,
		$php_ext
	)
	{
		$this->auth		    = $auth;
		$this->config       = $config;
		$this->db           = $db;
		$this->dispatcher   = $dispatcher;
		$this->template     = $template;
		$this->user		    = $user;
		$this->root_path    = $root_path;
		$this->php_ext      = $php_ext;
	}

	/**
	* Birthdays
	*
	* @return void
	*/
	public function birthdays(): void
	{
		$birthdays = [];

		$time = $this->user->create_datetime();
		$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

		// Display birthdays of 29th february on 28th february in non-leap-years
		$leap_year_birthdays = '';
		if ($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
		{
			$leap_year_birthdays = " OR u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
		}

		$sql_ary = $this->db->sql_build_query('SELECT', [
			'SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday',
			'FROM' => [
				USERS_TABLE => 'u',
			],
			'LEFT_JOIN' => [
				[
					'FROM' => [BANLIST_TABLE => 'b'],
					'ON' => 'u.user_id = b.ban_userid',
				],
			],
			'WHERE' => "(b.ban_id IS NULL OR b.ban_exclude = 1)
				AND (u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)
				AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')',
		]);

	    /**
	    * Event to modify the SQL query to get birthdays data
	    *
	    * @event core.index_modify_birthdays_sql
	    * @var	array	now			The assoc array with the 'now' local timestamp data
	    * @var	array	sql_ary		The SQL array to get the birthdays data
	    * @var	object	time		The user related Datetime object
	    * @since 3.1.7-RC1
	    */
	    $vars = array('now', 'sql_ary', 'time');
	    extract($this->dispatcher->trigger_event('core.index_modify_birthdays_sql', compact($vars)));

		$result = $this->db->sql_query($sql_ary);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		foreach ($rows as $row)
		{
			$birthday_username = get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']);
			$birthday_year = (int) substr($row['user_birthday'], -4);
			$birthday_age = ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';

			$birthdays[] = [
				'member' => $birthday_username,
				'age'	 => $birthday_age,
			];
		}

	    /**
	    * Event to modify the birthdays list
	    *
	    * @event core.index_modify_birthdays_list
	    * @var	array	birthdays		Array with the users birthdays data
	    * @var	array	rows			Array with the birthdays SQL query result
	    * @since 3.1.7-RC1
	    */
	    $vars = ['birthdays', 'rows'];
	    extract($this->dispatcher->trigger_event('core.index_modify_birthdays_list', compact($vars)));

		$this->template->assign_block_vars_array('birthdays', $birthdays);
	}

	/**
	* Legend
	*
	* @return void
	*/
	public function legend(): void
	{
		$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';

		// Grab group details for legend display
		$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type, g.group_legend
			FROM ' . GROUPS_TABLE . ' g
			LEFT JOIN ' . USER_GROUP_TABLE . ' ug
				ON (
					g.group_id = ug.group_id
					AND ug.user_id = ' . (int) $this->user->data['user_id'] . '
					AND ug.user_pending = 0
				)
			WHERE g.group_legend > 0
				AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . (int) $this->user->data['user_id'] . ')
			ORDER BY g.' . $order_legend;

		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
				FROM ' . GROUPS_TABLE . '
				WHERE group_legend > 0
				ORDER BY ' . $order_legend;
		}
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('legend', [
				'color' => (string) $row['group_colour'],
				'name'	=> (string) $row['group_name'],
				'link'	=> (string) append_sid("{$this->root_path}memberlist.$this->php_ext", "mode=group&amp;g={$row['group_id']}"),
				'not_authed' => (bool) $this->not_authed($row),
			]);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Show birthdays if available
	*
	* @return bool
	*/
	public function show_birthdays(): bool
	{
		return ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'));
	}

	/**
	* Is visitor a bot or does he/she have permissions
	*
	* @param array $row Groups data
	* @return bool
	*/
	protected function not_authed($row): bool
	{
		return $row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile'));
	}
}
