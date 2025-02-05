<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\event;

use ganstaz\gzo\src\enum\admin;
use ganstaz\gzo\src\helper;
use ganstaz\gzo\src\plugin\loader as plugins;
use ganstaz\gzo\src\user\page;
use ganstaz\gzo\src\user\loader as users_loader;
use phpbb\config\config;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\twig\twig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class subscribers implements EventSubscriberInterface
{
	public function __construct(
		protected config $config,
		protected controller $controller,
		protected language $language,
		protected request $request,
		protected twig $twig,
		protected plugins $plugins,
		protected helper $helper,
		protected page $page,
		protected users_loader $users_loader
	)
	{
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup'		 => 'add_language',
			'core.user_setup_after'	 => 'load_available_plugins',
			'core.page_header'		 => 'add_gzo_data',
			'core.page_header_after' => 'change_index',
			'core.acp_manage_forums_request_data'	 => 'manage_forums_request_data',
			'core.acp_manage_forums_display_form'	 => 'manage_forums_display_form',
			'core.memberlist_modify_viewprofile_sql' => 'redirect_profile',
			'core.memberlist_prepare_profile_data'	 => 'modify_profile_data',
			'core.modify_username_string'			 => 'modify_username_string',
			'core.posting_modify_template_vars'		 => 'submit_post_template',
		];
	}

	/**
	* Event core.user_setup
	*/
	public function add_language(): void
	{
		$this->language->add_lang('common', 'ganstaz/gzo');
	}

	/**
	* Event core.user_setup_after
	*/
	public function load_available_plugins(): void
	{
		if ($this->config['gzo_plugins'] && $page_name = $this->page->get_current_page())
		{
			// Set page variable
			$this->twig->assign_var('S_GZO_PAGE', true);

			// Load available plugins for a given page
			$this->plugins->load_available_plugins($page_name, $this->config);
		}
	}

	/**
	* Event core.page_header
	*/
	public function add_gzo_data(): void
	{
		//$current = $this->page->get_current_page();

		// if (!$this->page->is_control_panel($current) && $current === 'index')
		// {
		//	$url = $this->controller->route('ganstaz_gzo_forum');

		//	$response = new RedirectResponse($url);
		//	$response->send();
		// }

		if (defined(admin::GZO_IN_AREA))
		{
			$this->twig->assign_var('GZO_IN_AREA', true);
		}
	}

	/**
	* Event core.page_header_after
	*/
	public function change_index(): void
	{
		$this->twig->assign_vars([
			// 'U_INDEX' => $this->controller->route('ganstaz_gzo_forum'),
			'U_GZO_ADMIN' => $this->controller->route('gzo_main'),
		]);
	}

	/**
	* Event core.acp_manage_forums_request_data
	*/
	public function manage_forums_request_data($event): void
	{
		$forum_data = $event['forum_data'];
		$forum_data['news_fid_enable'] = $this->request->variable('news_fid_enable', 0);
		$event['forum_data'] = $forum_data;
	}

	/**
	* Event core.acp_manage_forums_display_form
	*/
	public function manage_forums_display_form($event): void
	{
		$template_data = $event['template_data'];
		$template_data['S_NEWS_FID'] = $event['forum_data']['news_fid_enable'];
		$event['template_data'] = $template_data;
	}

	/**
	* Event core.memberlist_modify_viewprofile_sql
	*/
	public function redirect_profile($event): void
	{
		if ($this->page->get_current_page() === 'memberlist')
		{
			$url = $this->controller->route('ganstaz_gzo_member', ['username' => $this->users_loader->get_username((int) $event['user_id'])]);

			$response = new RedirectResponse($url);
			$response->send();
		}
	}

	/**
	* Event core.memberlist_prepare_profile_data
	*/
	public function modify_profile_data($event): void
	{
		$data = $event['data'];
		$event['template_data'] = array_merge($event['template_data'], [
			'user_id'  => $data['user_id'],
			'username' => $data['username'],
			'color'	   => $data['user_colour'],
		]);
	}

	/**
	* Event core.modify_username_string
	*/
	public function modify_username_string($event): void
	{
		if ($event['mode'] === 'full')
		{
			$user  = $event['username'];
			$color = $event['username_colour'];
			$route = $this->controller->route('ganstaz_gzo_member', ['username' => $user]);

			// TODO: remove this html ASAP
			// Can be removed/modified when html part will be removed from phpBB
			$username_string = '<a href="' . $route . '" class="username">' . $user . '</a>';
			if ($color)
			{
				$username_string = '<a href="' . $route . '" style="color:' . $color . ';" class="username-coloured">' . $user . '</a>';
			}

			$event['username_string'] = $username_string;
		}
	}

	/**
	* Modify Special forum's posting page
	*/
	public function submit_post_template(): void
	{
		// Borrowed from Ideas extension (phpBB)
		// Alter posting page breadcrumbs to link to the ideas controller
		$this->twig->alter_block_array('navlinks', [
			'BREADCRUMB_NAME' => $this->language->lang('HOME'),
			'U_BREADCRUMB'	  => $this->controller->route('ganstaz_gzo_index'),
		], false, 'change');
	}
}
