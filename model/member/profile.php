<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\model\member;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\group\helper as group;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\profilefields\manager as cp;
use phpbb\template\template;
use phpbb\user;
use phpbb\exception\http_exception;

/**
* GZO Web: Member profile model
*/
class profile
{
	/** @var auth */
	protected $auth;

	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var dispatcher */
	protected $dispatcher;

	/** @var group */
	protected $group;

	/** @var controller helper */
	protected $controller;

	/** @var language */
	protected $language;

	/** @var profilefields manager */
	protected $cp;

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
	* @param config			  $config     Config object
	* @param driver_interface $db         Database object
	* @param dispatcher		  $dispatcher Dispatcher object
	* @param group            $group      Group helper object
	* @param controller       $controller Controller helper object
	* @param language         $language   Language object
	* @param cp               $cp         Profilefields manager object
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
		group $group,
		controller $controller,
		language $language,
		cp $cp,
		$template,
		user $user,
		$root_path,
		$php_ext
	)
	{
		$this->auth		  = $auth;
		$this->config     = $config;
		$this->db         = $db;
		$this->dispatcher = $dispatcher;
		$this->group      = $group;
		$this->controller = $controller;
		$this->language	  = $language;
		$this->cp         = $cp;
		$this->template   = $template;
		$this->user		  = $user;
		$this->root_path  = $root_path;
		$this->php_ext    = $php_ext;
	}

	/**
	* Get user data
	*
	* @param string $username
	* @return array
	*/
	public function get_user_data($username): array
	{
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

		return $member;
	}

	/**
	* View profile
	*
	* @param string $username
	* @return void
	*/
	public function view_profile($username): void
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

		$member = $this->get_user_data($username);

		// a_user admins and founder are able to view inactive users and bots to be able to manage them more easily
		// Normal users are able to see at least users having only changed their profile settings but not yet reactivated.
		if (!$this->auth->acl_get('a_user') && $user->data['user_type'] != USER_FOUNDER)
		{
			if ($member['user_type'] == USER_IGNORE)
			{
				throw new http_exception(404, 'NO_USER');
			}
			else if ($member['user_type'] == USER_INACTIVE && $member['user_inactive_reason'] != INACTIVE_PROFILE)
			{
				throw new http_exception(404, 'NO_USER');
			}
		}

		$user_id = (int) $member['user_id'];

		// Get group memberships
		// Also get visiting user's groups to determine hidden group memberships if necessary.
		$auth_hidden_groups = ($user_id === (int) $this->user->data['user_id'] || $this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel')) ? true : false;
		$sql_uid_ary = ($auth_hidden_groups) ? [$user_id] : [$user_id, (int) $this->user->data['user_id']];

		// Do the SQL thang
		$sql_ary = [
			'SELECT'	=> 'g.group_id, g.group_name, g.group_type, ug.user_id',

			'FROM'		=> [
				GROUPS_TABLE => 'g',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [USER_GROUP_TABLE => 'ug'],
					'ON'   => 'g.group_id = ug.group_id',
				],
			],

			'WHERE'		=> $this->db->sql_in_set('ug.user_id', $sql_uid_ary) . '
				AND ug.user_pending = 0',
		];

		/**
		* Modify the query used to get the group data
		*
		* @event core.modify_memberlist_viewprofile_group_sql
		* @var array	sql_ary			Array containing the query
		* @since 3.2.6-RC1
		*/
		$vars = [
			'sql_ary',
		];
		extract($this->dispatcher->trigger_event('core.modify_memberlist_viewprofile_group_sql', compact($vars)));

		$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));

		// Divide data into profile data and current user data
		$profile_groups = $user_groups = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$row['user_id'] = (int) $row['user_id'];
			$row['group_id'] = (int) $row['group_id'];

			if ($row['user_id'] == $user_id)
			{
				$profile_groups[] = $row;
			}
			else
			{
				$user_groups[$row['group_id']] = $row['group_id'];
			}
		}
		$this->db->sql_freeresult($result);

		// Filter out hidden groups and sort groups by name
		$group_data = $group_sort = [];
		foreach ($profile_groups as $row)
		{
			if (!$auth_hidden_groups && $row['group_type'] == GROUP_HIDDEN && !isset($user_groups[$row['group_id']]))
			{
				// Skip over hidden groups the user cannot see
				continue;
			}

			$row['group_name'] = $this->group->get_name($row['group_name']);

			$group_sort[$row['group_id']] = utf8_clean_string($row['group_name']);
			$group_data[$row['group_id']] = $row;
		}
		unset($profile_groups);
		unset($user_groups);
		asort($group_sort);

		/**
		* Modify group data before options is created and data is unset
		*
		* @event core.modify_memberlist_viewprofile_group_data
		* @var array	group_data			Array containing the group data
		* @var array	group_sort			Array containing the sorted group data
		* @since 3.2.6-RC1
		*/
		$vars = [
			'group_data',
			'group_sort',
		];
		extract($this->dispatcher->trigger_event('core.modify_memberlist_viewprofile_group_data', compact($vars)));

		#TODO: HTML
		$group_options = '';
		foreach ($group_sort as $group_id => $null)
		{
			$row = $group_data[$group_id];

			$group_options .= '<option value="' . $row['group_id'] . '"' . (($row['group_id'] == $member['group_id']) ? ' selected="selected"' : '') . '>' . $row['group_name'] . '</option>';
		}
		unset($group_data);
		unset($group_sort);

		// What colour is the zebra
		$sql = 'SELECT friend, foe
			FROM ' . ZEBRA_TABLE . "
			WHERE zebra_id = $user_id
				AND user_id = {$this->user->data['user_id']}";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		$foe = $row ? (bool) $row['foe'] : false;
		$friend = $row ? (bool) $row['friend'] : false;

		$this->db->sql_freeresult($result);

		if ($this->config['load_onlinetrack'])
		{
			$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
				FROM ' . SESSIONS_TABLE . "
				WHERE session_user_id = $user_id";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$member['session_time'] = (isset($row['session_time'])) ? $row['session_time'] : 0;
			$member['session_viewonline'] = (isset($row['session_viewonline'])) ? $row['session_viewonline'] : 0;
			unset($row);
		}

		// Display a listing of board admins, moderators
		if (!function_exists('display_user_activity'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}

		if ($this->config['load_user_activity'])
		{
			display_user_activity($member);
		}

		// Do the relevant calculations
		$memberdays = max(1, round((time() - $member['user_regdate']) / 86400));
		$posts_per_day = $member['user_posts'] / $memberdays;
		$percentage = ($this->config['num_posts']) ? min(100, ($member['user_posts'] / $this->config['num_posts']) * 100) : 0;

		if ($member['user_sig'])
		{
			$parse_flags = ($member['user_sig_bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
			$member['user_sig'] = generate_text_for_display($member['user_sig'], $member['user_sig_bbcode_uid'], $member['user_sig_bbcode_bitfield'], $parse_flags, true);
		}

		// We need to check if the modules 'zebra' ('friends' & 'foes' mode),  'notes' ('user_notes' mode) and  'warn' ('warn_user' mode) are accessible to decide if we can display appropriate links
		$zebra_enabled = $friends_enabled = $foes_enabled = $user_notes_enabled = $warn_user_enabled = false;

		// Only check if the user is logged in
		if ($this->user->data['is_registered'])
		{
			// TODO: I don't like this module manager
			if (!class_exists('p_master'))
			{
				include($this->root_path . 'includes/functions_module.' . $this->php_ext);
			}
			$module = new \p_master();

			$module->list_modules('ucp');
			$module->list_modules('mcp');

			$user_notes_enabled = ($module->loaded('mcp_notes', 'user_notes')) ? true : false;
			$warn_user_enabled = ($module->loaded('mcp_warn', 'warn_user')) ? true : false;
			$zebra_enabled = ($module->loaded('ucp_zebra')) ? true : false;
			$friends_enabled = ($module->loaded('ucp_zebra', 'friends')) ? true : false;
			$foes_enabled = ($module->loaded('ucp_zebra', 'foes')) ? true : false;

			unset($module);
		}

		// Custom Profile Fields
		$profile_fields = [];
		if ($this->config['load_cpf_viewprofile'])
		{
			$profile_fields = $this->cp->grab_profile_fields_data($user_id);
			$profile_fields = (isset($profile_fields[$user_id])) ? $this->cp->generate_profile_fields_template_data($profile_fields[$user_id]) : [];
		}

		/**
		* Modify user data before we display the profile
		*
		* @event core.memberlist_view_profile
		* @var	array	member					Array with user's data
		* @var	bool	user_notes_enabled		Is the mcp user notes module enabled?
		* @var	bool	warn_user_enabled		Is the mcp warnings module enabled?
		* @var	bool	zebra_enabled			Is the ucp zebra module enabled?
		* @var	bool	friends_enabled			Is the ucp friends module enabled?
		* @var	bool	foes_enabled			Is the ucp foes module enabled?
		* @var	bool    friend					Is the user friend?
		* @var	bool	foe						Is the user foe?
		* @var	array	profile_fields			Array with user's profile field data
		* @since 3.1.0-a1
		* @changed 3.1.0-b2 Added friend and foe status
		* @changed 3.1.0-b3 Added profile fields data
		*/
		$vars = [
			'member',
			'user_notes_enabled',
			'warn_user_enabled',
			'zebra_enabled',
			'friends_enabled',
			'foes_enabled',
			'friend',
			'foe',
			'profile_fields',
		];
		extract($this->dispatcher->trigger_event('core.memberlist_view_profile', compact($vars)));

		$this->template->assign_vars(phpbb_show_profile($member, $user_notes_enabled, $warn_user_enabled));

		// If the user has m_approve permission or a_user permission, then list then display unapproved posts
		if ($this->auth->acl_getf_global('m_approve') || $this->auth->acl_get('a_user'))
		{
			$sql = 'SELECT COUNT(post_id) as posts_in_queue
				FROM ' . POSTS_TABLE . '
				WHERE poster_id = ' . $user_id . '
					AND ' . $this->db->sql_in_set('post_visibility', [ITEM_UNAPPROVED, ITEM_REAPPROVE]);
			$result = $this->db->sql_query($sql);
			$member['posts_in_queue'] = (int) $this->db->sql_fetchfield('posts_in_queue');
			$this->db->sql_freeresult($result);
		}
		else
		{
			$member['posts_in_queue'] = 0;
		}

		// Define the main array of vars to assign to memberlist_view.html
		$template_ary = [
			'L_POSTS_IN_QUEUE'			=> $this->language->lang('NUM_POSTS_IN_QUEUE', $member['posts_in_queue']),

			'POSTS_DAY'					=> $this->language->lang('POST_DAY', $posts_per_day),
			'POSTS_PCT'					=> $this->language->lang('POST_PCT', $percentage),

			'SIGNATURE'					=> $member['user_sig'],
			'POSTS_IN_QUEUE'			=> $member['posts_in_queue'],

			// TODO: Change icons to FA
			'PM_IMG'					=> $this->user->img('icon_contact_pm', $this->language->lang('SEND_PRIVATE_MESSAGE')),
			'L_SEND_EMAIL_USER'			=> $this->user->lang('SEND_EMAIL_USER', $member['username']),
			'EMAIL_IMG'					=> $this->user->img('icon_contact_email', $this->language->lang('EMAIL')),
			'JABBER_IMG'				=> $this->user->img('icon_contact_jabber', $this->language->lang('JABBER')),
			'SEARCH_IMG'				=> $this->user->img('icon_user_search', $this->language->lang('SEARCH')),

			'S_PROFILE_ACTION'			=> append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=group'),
			'S_GROUP_OPTIONS'			=> $group_options,
			'S_CUSTOM_FIELDS'			=> (isset($profile_fields['row']) && count($profile_fields['row'])) ? true : false,

			// TODO: wrong link
			'U_USER_ADMIN'				=> ($this->auth->acl_get('a_user')) ? append_sid("{$this->root_path}index.$this->php_ext", 'i=users&amp;mode=overview&amp;u=' . $user_id, true, $this->user->session_id) : '',

			'U_USER_BAN'				=> ($this->auth->acl_get('m_ban') && $user_id != $this->user->data['user_id']) ? append_sid("{$this->root_path}mcp.$this->php_ext", 'i=ban&amp;mode=user&amp;u=' . $user_id, true, $this->user->session_id) : '',
			'U_MCP_QUEUE'				=> ($this->auth->acl_getf_global('m_approve')) ? append_sid("{$this->root_path}mcp.$this->php_ext", 'i=queue', true, $this->user->session_id) : '',

			'U_SWITCH_PERMISSIONS'		=> ($this->auth->acl_get('a_switchperm') && $this->user->data['user_id'] != $user_id) ? append_sid("{$this->root_path}ucp.$this->php_ext", "mode=switch_perm&amp;u={$user_id}&amp;hash=" . generate_link_hash('switchperm')) : '',
			'U_EDIT_SELF'				=> ($user_id == $this->user->data['user_id'] && $this->auth->acl_get('u_chgprofileinfo')) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=ucp_profile&amp;mode=profile_info') : '',

			'S_USER_NOTES'				=> ($user_notes_enabled) ? true : false,
			'S_WARN_USER'				=> ($warn_user_enabled) ? true : false,
			'S_ZEBRA'					=> ($this->user->data['user_id'] != $user_id && $this->user->data['is_registered'] && $zebra_enabled) ? true : false,
			'U_ADD_FRIEND'				=> (!$friend && !$foe && $friends_enabled) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=zebra&amp;add=' . urlencode(html_entity_decode($member['username'], ENT_COMPAT))) : '',
			'U_ADD_FOE'					=> (!$friend && !$foe && $foes_enabled) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=zebra&amp;mode=foes&amp;add=' . urlencode(html_entity_decode($member['username'], ENT_COMPAT))) : '',
			'U_REMOVE_FRIEND'			=> ($friend && $friends_enabled) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=zebra&amp;remove=1&amp;usernames[]=' . $user_id) : '',
			'U_REMOVE_FOE'				=> ($foe && $foes_enabled) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'i=zebra&amp;remove=1&amp;mode=foes&amp;usernames[]=' . $user_id) : '',

			// TODO: WTF is this?
			'U_CANONICAL'				=> generate_board_url() . '/' . append_sid("memberlist.$this->php_ext", 'mode=viewprofile&amp;u=' . $user_id, true, ''),
		];

		/**
		* Modify user's template vars before we display the profile
		*
		* @event core.memberlist_modify_view_profile_template_vars
		* @var	array	template_ary	Array with user's template vars
		* @since 3.2.6-RC1
		*/
		$vars = [
			'template_ary',
		];
		extract($this->dispatcher->trigger_event('core.memberlist_modify_view_profile_template_vars', compact($vars)));

		// Assign vars to profile.twig
		$this->template->assign_vars($template_ary);

		if (!empty($profile_fields['row']))
		{
			$this->template->assign_vars($profile_fields['row']);
		}

		if (!empty($profile_fields['blockrow']))
		{
			foreach ($profile_fields['blockrow'] as $field_data)
			{
				$this->template->assign_block_vars('custom_fields', $field_data);
			}
		}

		// Inactive reason/account?
		if ($member['user_type'] == USER_INACTIVE)
		{
			$this->language->add_lang('acp/common');

			$inactive_reason = $this->language->lang('INACTIVE_REASON_UNKNOWN');

			switch ($member['user_inactive_reason'])
			{
				case INACTIVE_REGISTER:
					$inactive_reason = $this->language->lang('INACTIVE_REASON_REGISTER');
				break;

				case INACTIVE_PROFILE:
					$inactive_reason = $this->language->lang('INACTIVE_REASON_PROFILE');
				break;

				case INACTIVE_MANUAL:
					$inactive_reason = $this->language->lang('INACTIVE_REASON_MANUAL');
				break;

				case INACTIVE_REMIND:
					$inactive_reason = $this->language->lang('INACTIVE_REASON_REMIND');
				break;
			}

			$this->template->assign_vars([
				'S_USER_INACTIVE'		=> true,
				'USER_INACTIVE_REASON'	=> $inactive_reason]
			);
		}

		$this->template->assign_block_vars('navlinks', [
			'BREADCRUMB_NAME'	=> $this->language->lang('MEMBERLIST'),
			'U_BREADCRUMB'		=> append_sid("{$this->root_path}memberlist.$this->php_ext"),
		]);
		$this->template->assign_block_vars('navlinks', [
			'BREADCRUMB_NAME'	=> $member['username'],
			'U_BREADCRUMB'		=> $this->controller->route('ganstaz_web_member', ['username' => $username]),
		]);

		// TODO: Remove it?
		make_jumpbox(append_sid("{$this->root_path}viewforum.$this->php_ext"));
	}
}