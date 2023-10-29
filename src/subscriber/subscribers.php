<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\subscriber;

use phpbb\config\config;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use ganstaz\gzo\src\helper;
use ganstaz\gzo\src\pages;
use ganstaz\gzo\src\blocks\manager;
use ganstaz\gzo\src\enum\admin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event subscribers
*/
class subscribers implements EventSubscriberInterface
{
	public function __construct(
		private readonly config $config,
		private readonly controller $controller,
		private readonly language $language,
		private readonly request $request,
		private readonly template $template,
		private readonly helper $helper,
		private readonly pages $pages,
		private readonly manager $manager
	)
	{
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*/
	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup'		 => 'add_language',
			'core.user_setup_after'	 => 'add_manager_data',
			'core.page_header'		 => 'add_gzo_data',
			'core.page_header_after' => 'change_index',
			'core.viewforum_get_topic_data'			 => 'news_forum_redirect',
			'core.posting_modify_template_vars'		 => 'submit_post_template',
			'core.acp_manage_forums_request_data'	 => 'manage_forums_request_data',
			'core.acp_manage_forums_display_form'	 => 'manage_forums_display_form',
			'core.memberlist_modify_viewprofile_sql' => 'redirect_profile',
			'core.modify_username_string'			 => 'modify_username_string',
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
	public function add_manager_data(): void
	{
		if ($this->config['gzo_blocks'] && $get_page_data = $this->pages->get_page_data())
		{
			// Set page var for template, so we know where we are
			$this->template->assign_var('S_GZO_PAGE', true);

			// Load available blocks
			$this->manager->load($get_page_data);

			// Assign template vars
			foreach ($get_page_data as $s_page)
			{
				$this->template->assign_vars([
					$s_page => $this->manager->has($s_page),
				]);
			}
		}
	}

	/**
	* Event core.page_header
	*/
	public function add_gzo_data(): void
	{
		$current = $this->pages->get_current_page();

		if (!$this->pages->is_cp($current) && $current === 'index')
		{
			$url = $this->controller->route('ganstaz_gzo_forum');

			$response = new RedirectResponse($url);
			$response->send();
		}

		if (defined(admin::GZO_IN_AREA))
		{
			$this->template->assign_var('GZO_IN_AREA', true);
		}

		if ($this->config['gzo_news_link'])
		{
			$this->template->assign_vars([
				'U_NEWS' => $this->controller->route('ganstaz_gzo_news'),
			]);
		}
	}

	/**
	* Event core.page_header_after
	*/
	public function change_index(): void
	{
		$this->template->assign_vars([
			'U_INDEX' => $this->controller->route('ganstaz_gzo_forum'),
			'U_GZO_ADMIN' => $this->controller->route('gzo_main'),
		]);
	}

	/**
	* Redirect users from the forum to the right controller
	*/
	public function news_forum_redirect($event): void
	{
		$forum_id = (int) $event['forum_id'];

		// Will redirect to our controller
		if (in_array($forum_id, $this->helper->get_forum_ids()) && $forum_id !== (int) $this->config['gzo_main_fid'])
		{
			$url = $this->controller->route('ganstaz_gzo_news', ['id' => $forum_id]);

			$response = new RedirectResponse($url);
			$response->send();
		}
	}

	/**
	* Modify Special forum's posting page
	*/
	public function submit_post_template(): void
	{
		// Borrowed from Ideas extension (phpBB)
		// Alter posting page breadcrumbs to link to the ideas controller
		$this->template->alter_block_array('navlinks', [
			'BREADCRUMB_NAME' => $this->language->lang('HOME'),
			'U_BREADCRUMB'	  => $this->controller->route('ganstaz_gzo_index'),
		], false, 'change');
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
		if ($this->pages->get_current_page() === 'memberlist')
		{
			$url = $this->controller->route('ganstaz_gzo_member', ['username' => $this->helper->get_user_name((int) $event['user_id'])]);

			$response = new RedirectResponse($url);
			$response->send();
		}
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
}
