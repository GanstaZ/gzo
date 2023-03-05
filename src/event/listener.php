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

use phpbb\config\config;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use ganstaz\gzo\src\helper;
use ganstaz\gzo\src\pages;
use ganstaz\gzo\src\blocks\manager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var controller */
	protected $controller;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var helper */
	protected $helper;

	/** @var pages */
	protected $pages;

	/** @var manager */
	protected $manager;

	/**
	* Constructor
	*
	* @param config		$config		Config object
	* @param controller $controller Controller helper object
	* @param language	$language	Language object
	* @param request	$request	Request object
	* @param template	$template	Template object
	* @param helper		$helper		Helper object
	* @param pages		$pages		Pages object
	* @param manager	$manager	Blocks manager object
	*/
	public function __construct(
		config $config,
		controller $controller,
		language $language,
		request $request,
		template $template,
		helper $helper,
		pages $pages,
		manager $manager = null
	)
	{
		$this->config	  = $config;
		$this->controller = $controller;
		$this->language	  = $language;
		$this->request	  = $request;
		$this->template	  = $template;
		$this->helper	  = $helper;
		$this->pages	  = $pages;
		$this->manager	  = $manager;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	*/
	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup'		 => 'add_language',
			'core.user_setup_after'	 => 'add_manager_data',
			'core.page_header'		 => 'add_web_data',
			'core.page_header_after' => 'change_index',
			'core.viewforum_get_topic_data'			 => 'news_forum_redirect',
			'core.posting_modify_template_vars'		 => 'submit_post_template',
			'core.acp_manage_forums_request_data'	 => 'manage_forums_request_data',
			'core.acp_manage_forums_display_form'	 => 'manage_forums_display_form',
			'core.memberlist_modify_viewprofile_sql' => 'redirect_profile',
			'core.modify_username_string'			 => 'modify_username_string'
		];
	}

	/**
	* Event core.user_setup
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function add_language($event): void
	{
		// Load a single language file from ganstaz/gzo/language/en/common.php
		$this->language->add_lang('common', 'ganstaz/gzo');
	}

	/**
	* Event core.user_setup_after
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function add_manager_data($event): void
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
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function add_web_data(): void
	{
		$current = $this->pages->get_current_page();

		if (!$this->pages->is_cp($current) && $current === 'index')
		{
			$url = $this->controller->route('ganstaz_gzo_forum');

			$response = new RedirectResponse($url);
			$response->send();
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
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function change_index(): void
	{
		$this->template->assign_vars([
			'U_INDEX' => $this->controller->route('ganstaz_gzo_forum'),
		]);
	}

	/**
	* Redirect users from the forum to the right controller
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function news_forum_redirect($event)
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
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function submit_post_template($event)
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
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function manage_forums_request_data($event): void
	{
		$forum_data = $event['forum_data'];
		$forum_data['news_fid_enable'] = $this->request->variable('news_fid_enable', 0);
		$event['forum_data'] = $forum_data;
	}

	/**
	* Event core.acp_manage_forums_display_form
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function manage_forums_display_form($event): void
	{
		$template_data = $event['template_data'];
		$template_data['S_NEWS_FID'] = $event['forum_data']['news_fid_enable'];
		$event['template_data'] = $template_data;
	}

	/**
	* Event core.memberlist_modify_viewprofile_sql
	*
	* @param \phpbb\event\data $event The event object
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
	*
	* @param \phpbb\event\data $event The event object
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
