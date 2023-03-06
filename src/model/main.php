<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\model;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\event\dispatcher;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\notification\manager as notifications;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use ganstaz\gzo\src\info;
use phpbb\exception\http_exception;

/**
* Forum index model
*/
class main
{
	/** @var auth */
	protected $auth;

	/** @var config */
	protected $config;

	/** @var dispatcher */
	protected $dispatcher;

	/** @var controller helper */
	protected $controller;

	/** @var language */
	protected $language;

	/** @var notifications */
	protected $notifications;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var info */
	protected $info;

	/** @var root_path */
	protected $root_path;

	/** @var php_ext */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param auth			  $auth		  Auth object
	* @param config			  $config	  Config object
	* @param dispatcher		  $dispatcher Dispatcher object
	* @param controller		  $controller Controller helper object
	* @param language		  $language	  Language object
	* @param template		  $template	  Template object
	* @param user			  $user		  User object
	* @param string			  $root_path  Path to the phpbb includes directory
	* @param string			  $php_ext	  PHP file extension
	*/
	public function __construct
	(
		auth $auth,
		config $config,
		dispatcher $dispatcher,
		controller $controller,
		language $language,
		notifications $notifications,
		request $request,
		template $template,
		user $user,
		info $info,
		$root_path,
		$php_ext
	)
	{
		$this->auth			 = $auth;
		$this->config		 = $config;
		$this->dispatcher	 = $dispatcher;
		$this->controller	 = $controller;
		$this->language		 = $language;
		$this->notifications = $notifications;
		$this->request		 = $request;
		$this->template		 = $template;
		$this->user			 = $user;
		$this->info			 = $info;
		$this->root_path	 = $root_path;
		$this->php_ext		 = $php_ext;
	}

	public function load()
	{
		// Mark notifications read
		if (($mark_notification = $this->request->variable('mark_notification', 0)))
		{
			if ($this->user->data['user_id'] == ANONYMOUS)
			{
				if ($this->request->is_ajax())
				{
					throw new http_exception(404, 'LOGIN_REQUIRED');
				}
				login_box('', $this->language->lang('LOGIN_REQUIRED'));
			}

			if (check_link_hash($this->request->variable('hash', ''), 'mark_notification_read'))
			{
				$notification = $this->notifications->load_notifications('notification.method.board', [
					'notification_id'	=> $mark_notification,
				]);

				if (isset($notification['notifications'][$mark_notification]))
				{
					$notification = $notification['notifications'][$mark_notification];

					$notification->mark_read();

					/**
					* You can use this event to perform additional tasks or redirect user elsewhere.
					*
					* @event core.index_mark_notification_after
					* @var	int										mark_notification	Notification ID
					* @var	\phpbb\notification\type\type_interface	notification		Notification instance
					* @since 3.2.6-RC1
					*/
					$vars = ['mark_notification', 'notification'];
					extract($this->dispatcher->trigger_event('core.index_mark_notification_after', compact($vars)));

					if ($this->request->is_ajax())
					{
						$json_response = new \phpbb\json_response();
						$json_response->send([
							'success'	=> true,
						]);
					}

					// TODO: Should use redirectresponse
					if (($redirect = $this->request->variable('redirect', '')))
					{
						redirect(append_sid($this->root_path . $redirect));
					}

					redirect($notification->get_redirect_url());
				}
			}
		}

		if (!function_exists('display_forums'))
		{
			include("{$this->root_path}includes/functions_display.$this->php_ext");
		}

		display_forums('', $this->config['load_moderators']);

		// Generate birthday list if required...
		if ($this->info->show_birthdays())
		{
			$this->info->birthdays();
		}

		$this->info->legend();

		// Assign index specific vars
		$this->template->assign_vars([
			'TOTAL_POSTS'  => (int) $this->config['num_posts'],
			'TOTAL_TOPICS' => (int) $this->config['num_topics'],
			'TOTAL_USERS'  => (int) $this->config['num_users'],
			'NEWEST_USER'  => get_username_string('full', (int) $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour']),

			'S_LOGIN_ACTION'  => append_sid("{$this->root_path}ucp.$this->php_ext", 'mode=login'),
			'U_SEND_PASSWORD' => ($this->config['email_enable'] && $this->config['allow_password_reset']) ? $this->controller->route('phpbb_ucp_forgot_password_controller') : '',
			'S_DISPLAY_BIRTHDAY_LIST' => $this->info->show_birthdays(),
			'S_INDEX'		  => true,

			'U_MARK_FORUMS'	  => ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? append_sid("{$this->root_path}index.$this->php_ext", 'hash=' . generate_link_hash('global') . '&amp;mark=forums&amp;mark_time=' . time()) : '',
			'U_MCP'			  => ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_')) ? append_sid("{$this->root_path}mcp.$this->php_ext", 'i=main&amp;mode=front', true, $this->user->session_id) : ''
		]);
	}
}
