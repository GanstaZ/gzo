<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\controller\old;

use phpbb\cache\service as cache;
use phpbb\db\driver\driver_interface as driver;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;

/**
* Admin pages controller
*/
class pages
{
	/** @var cache */
	protected $cache;

	/** @var driver */
	protected $db;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var page table */
	protected $page_data;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param cache	  $cache	 Cache object
	* @param driver	  $db		 Database object
	* @param language $language	 Language object
	* @param request  $request	 Request object
	* @param template $template	 Template object
	* @param string	  $page_data The name of the page data table
	*/
	public function __construct(
		cache $cache,
		driver $db,
		language $language,
		request $request,
		template $template,
		$page_data
	)
	{
		$this->cache	 = $cache;
		$this->db		 = $db;
		$this->language	 = $language;
		$this->request	 = $request;
		$this->template	 = $template;
		$this->page_data = $page_data;
	}

	/**
	* Display page
	*
	* @return void
	*/
	public function display_page(): void
	{
		// Add form key for form validation checks
		add_form_key('ganstaz_gzo_pages');

		//$this->language->add_lang('acp_pages', 'ganstaz/gzo');

		// Get all pages
		$sql = 'SELECT name, active, allow, changeable, gzo_right, gzo_left, gzo_middle, gzo_top, gzo_bottom
				FROM ' . $this->page_data . '
				ORDER BY id';
		$result = $this->db->sql_query($sql);

		$pages = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$pages[$row['name']] = [
				'name'		 => $row['name'],
				'active'	 => (bool) $row['active'],
				'allow'		 => (bool) $row['allow'],
				'changeable' => (bool) $row['changeable'],
				'gzo_right'	 => (bool) $row['gzo_right'],
				'gzo_left'	 => (bool) $row['gzo_left'],
				'gzo_middle' => (bool) $row['gzo_middle'],
				'gzo_top'	 => (bool) $row['gzo_top'],
				'gzo_bottom' => (bool) $row['gzo_bottom'],
			];
		}
		$this->db->sql_freeresult($result);

		// Is the form submitted
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('ganstaz_gzo_pages'))
			{
				trigger_error('FORM_INVALID');
			}

			// If the form has been submitted, set all data and save it
			$this->update_data($pages);

			$this->cache->destroy('_gzo_pages');

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_GZO_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set output vars for display in the template
		$this->assign_template_page_data($pages);

		// Set template vars
		$this->template->assign_vars([
			'S_GZO_PAGE' => true,
			'U_ACTION'	 => $this->u_action,
		]);
	}

	/**
	* Update data
	*
	* @param array $pages Array of pages data
	* @return void
	*/
	public function update_data(array $pages): void
	{
		foreach ($pages as $data)
		{
			$page = $this->request->variable($data['name'], (bool) 0);

			$page_data = [
				'active'	 => $this->request->variable($data['name'] . '_active', (bool) 0),
				'allow'		 => $this->request->variable($data['name'] . '_allow', (bool) 0),
				'gzo_right'	 => $this->request->variable($data['name'] . '_right', (bool) 0),
				'gzo_left'	 => $this->request->variable($data['name'] . '_left', (bool) 0),
				'gzo_middle' => $this->request->variable($data['name'] . '_middle', (bool) 0),
				'gzo_top'	 => $this->request->variable($data['name'] . '_top', (bool) 0),
				'gzo_bottom' => $this->request->variable($data['name'] . '_bottom', (bool) 0),
			];

			if ($page)
			{
				// Update selected/requested page data
				$this->db->sql_query('UPDATE ' . $this->page_data . ' SET ' .
					$this->db->sql_build_array('UPDATE', $page_data) . "
					WHERE name = '" . $this->db->sql_escape($data['name']) . "'"
				);
			}
		}
	}

	/**
	* Assign template data for pages
	*
	* @param array $pages Pages data is stored here
	* @return void
	*/
	protected function assign_template_page_data(array $pages): void
	{
		foreach ($pages as $page => $data)
		{
			$this->template->assign_block_vars('pages', [
				'name'		 => $page,
				'active'	 => $data['active'],
				'allow'		 => $data['allow'],
				'right'		 => $data['gzo_right'],
				'left'		 => $data['gzo_left'],
				'middle'	 => $data['gzo_middle'],
				'top'		 => $data['gzo_top'],
				'bottom'	 => $data['gzo_bottom'],
				'changeable' => $data['changeable'],
			]);
		}
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
