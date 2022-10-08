<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\controller\admin;

use phpbb\config\config;

use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use ganstaz\web\core\helper;

/**
* GZO Web: admin settings controller
*/
class settings
{
	/** @var config */
	protected $config;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var helper */
	protected $helper;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param config	  $config	Config object
	* @param language $language Language object
	* @param request  $request	Request object
	* @param template $template Template object
	* @param helper	  $helper	Helper object
	*/
	public function __construct(config $config, language $language, request $request, template $template, helper $helper)
	{
		$this->config	= $config;
		$this->language = $language;
		$this->request	= $request;
		$this->template = $template;
		$this->helper	= $helper;
	}

	/**
	* Display web settings
	*
	* @return void
	*/
	public function display_web(): void
	{
		// Add form key for form validation checks
		add_form_key('ganstaz/web');

		$this->language->add_lang('acp_web', 'ganstaz/web');

		$s_forum_ids = count($this->helper->get_forum_ids()) > 1;

		// Is the form submitted
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('ganstaz/web'))
			{
				trigger_error('FORM_INVALID');
			}

			// If the form has been submitted, set all data and save it
			if ($s_forum_ids)
			{
				$this->config->set('gzo_main_fid', $this->request->variable('gzo_main_fid', (int) 0));
				$this->config->set('gzo_news_fid', $this->request->variable('gzo_news_fid', (int) 0));
			}

			$this->set_options();

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_GZO_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set template vars
		$this->template->assign_vars([
			'GZO_VERSION'		 => $this->config['gzo_core_version'],
			'GZO_NEWS_IDS'		 => $this->helper->get_forum_ids(),
			'S_NEWS_IDS'		 => $s_forum_ids,
			'S_MAIN_CURRENT'	 => $this->config['gzo_main_fid'],
			'S_NEWS_CURRENT'	 => $this->config['gzo_news_fid'],
			'S_NEWS_LINK'        => $this->config['gzo_news_link'],
			'S_PROFILE_TABS'	 => $this->config['gzo_profile_tabs'],
			'S_PAGINATION'		 => $this->config['gzo_pagination'],
			'GZO_LIMIT'			 => $this->config['gzo_limit'],
			'GZO_USER_LIMIT'	 => $this->config['gzo_user_limit'],
			'MIN_TITLE_LENGTH'	 => $this->config['gzo_title_length'],
			'MIN_CONTENT_LENGTH' => $this->config['gzo_content_length'],
			'S_BLOCKS'			 => $this->config['gzo_blocks'],
			'S_SPECIAL'			 => $this->config['gzo_special'],
			'S_RIGHT'			 => $this->config['gzo_right'],
			'S_LEFT'			 => $this->config['gzo_left'],
			'S_MIDDLE'			 => $this->config['gzo_middle'],
			'S_TOP'				 => $this->config['gzo_top'],
			'S_BOTTOM'			 => $this->config['gzo_bottom'],
			'U_ACTION'			 => $this->u_action,
		]);
	}

	/**
	* Set config options
	*
	* @return void
	*/
	protected function set_options(): void
	{
		$this->config->set('gzo_news_link', $this->request->variable('gzo_news_link', (bool) 0));
		//$this->config->set('gzo_the_team_fid', $this->request->variable('gzo_the_team_fid', (int) 0));
		//$this->config->set('gzo_top_posters_fid', $this->request->variable('gzo_top_posters_fid', (int) 0));
		//$this->config->set('gzo_recent_posts_fid', $this->request->variable('gzo_recent_posts_fid', (int) 0));
		//$this->config->set('gzo_recent_topics_fid', $this->request->variable('gzo_recent_topics_fid', (int) 0));
		$this->config->set('gzo_profile_tabs', $this->request->variable('gzo_profile_tabs', (bool) 0));
		$this->config->set('gzo_pagination', $this->request->variable('gzo_pagination', (bool) 0));
		$this->config->set('gzo_title_length', $this->request->variable('gzo_title_length', (int) 0));
		$this->config->set('gzo_content_length', $this->request->variable('gzo_content_length', (int) 0));
		$this->config->set('gzo_limit', $this->request->variable('gzo_limit', (int) 0));
		$this->config->set('gzo_user_limit', $this->request->variable('gzo_user_limit', (int) 0));
		$this->config->set('gzo_blocks', $this->request->variable('gzo_blocks', (bool) 0));
		$this->config->set('gzo_special', $this->request->variable('gzo_special', (bool) 0));
		$this->config->set('gzo_right', $this->request->variable('gzo_right', (bool) 0));
		$this->config->set('gzo_left', $this->request->variable('gzo_left', (bool) 0));
		$this->config->set('gzo_middle', $this->request->variable('gzo_middle', (bool) 0));
		$this->config->set('gzo_top', $this->request->variable('gzo_top', (bool) 0));
		$this->config->set('gzo_bottom', $this->request->variable('gzo_bottom', (bool) 0));
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return self
	*/
	public function set_page_url(string $u_action): self
	{
		$this->u_action = $u_action;

		return $this;
	}
}
