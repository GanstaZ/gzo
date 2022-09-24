<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
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
* GZ Web: admin settings controller
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
	* @param helper	  $helper   Helper object
	*/
	public function __construct(config $config, language $language, request $request, template $template, helper $helper)
	{
		$this->config   = $config;
		$this->language = $language;
		$this->request  = $request;
		$this->template = $template;
		$this->helper   = $helper;
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

		// Is the form submitted
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('ganstaz/web'))
			{
				trigger_error('FORM_INVALID');
			}

			// If the form has been submitted, set all data and save it
			$this->set_options();

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_GZ_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set template vars
		$this->template->assign_vars([
			'GZ_VERSION'		 => $this->config['gz_core_version'],
			'GZ_NEWS_IDS'		 => $this->helper->get_forum_ids(),
			'S_NEWS_IDS'	     => count($this->helper->get_forum_ids()) > 1,
			'S_MAIN_CURRENT'	 => $this->config['gz_main_fid'],
			'S_NEWS_CURRENT'	 => $this->config['gz_news_fid'],
			'S_ENABLE_NEWS_LINK' => $this->config['gz_enable_news_link'],
			'S_PROFILE_TABS'     => $this->config['gz_profile_tabs'],
			'S_PAGINATION'		 => $this->config['gz_pagination'],
			'GZ_LIMIT'			 => $this->config['gz_limit'],
			'GZ_USER_LIMIT'	     => $this->config['gz_user_limit'],
			'MIN_TITLE_LENGTH'	 => $this->config['gz_title_length'],
			'MIN_CONTENT_LENGTH' => $this->config['gz_content_length'],
			'S_BLOCKS'			 => $this->config['gz_blocks'],
			'S_SPECIAL'			 => $this->config['gz_special'],
			'S_RIGHT'			 => $this->config['gz_right'],
			'S_LEFT'			 => $this->config['gz_left'],
			'S_MIDDLE'			 => $this->config['gz_middle'],
			'S_TOP'				 => $this->config['gz_top'],
			'S_BOTTOM'			 => $this->config['gz_bottom'],
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
		$this->config->set('gz_main_fid', $this->request->variable('gz_main_fid', (int) 0));
		$this->config->set('gz_news_fid', $this->request->variable('gz_news_fid', (int) 0));
		$this->config->set('gz_enable_news_link', $this->request->variable('gz_enable_news_link', (bool) 0));
		//$this->config->set('gz_the_team_fid', $this->request->variable('gz_the_team_fid', (int) 0));
		//$this->config->set('gz_top_posters_fid', $this->request->variable('gz_top_posters_fid', (int) 0));
		//$this->config->set('gz_recent_posts_fid', $this->request->variable('gz_recent_posts_fid', (int) 0));
		//$this->config->set('gz_recent_topics_fid', $this->request->variable('gz_recent_topics_fid', (int) 0));
		$this->config->set('gz_profile_tabs', $this->request->variable('gz_profile_tabs', (bool) 0));
		$this->config->set('gz_pagination', $this->request->variable('gz_pagination', (bool) 0));
		$this->config->set('gz_title_length', $this->request->variable('gz_title_length', (int) 0));
		$this->config->set('gz_content_length', $this->request->variable('gz_content_length', (int) 0));
		$this->config->set('gz_limit', $this->request->variable('gz_limit', (int) 0));
		$this->config->set('gz_user_limit', $this->request->variable('gz_user_limit', (int) 0));
		$this->config->set('gz_blocks', $this->request->variable('gz_blocks', (bool) 0));
		$this->config->set('gz_special', $this->request->variable('gz_special', (bool) 0));
		$this->config->set('gz_right', $this->request->variable('gz_right', (bool) 0));
		$this->config->set('gz_left', $this->request->variable('gz_left', (bool) 0));
		$this->config->set('gz_middle', $this->request->variable('gz_middle', (bool) 0));
		$this->config->set('gz_top', $this->request->variable('gz_top', (bool) 0));
		$this->config->set('gz_bottom', $this->request->variable('gz_bottom', (bool) 0));
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
