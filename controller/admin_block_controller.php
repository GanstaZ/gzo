<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\controller;

use Symfony\Component\DependencyInjection\ContainerInterface as container;
use phpbb\db\driver\driver_interface as driver;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use dls\web\core\blocks\manager;

/**
* DLS Web admin block controller
*/
class admin_block_controller
{
	/** @var container */
	protected $container;

	/** @var driver */
	protected $db;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var manager */
	protected $manager;

	/** @var array Contains info about current status */
	protected $status;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param container $container A container
	* @param driver	   $db		  Database object
	* @param language  $language  Language object
	* @param request   $request	  Request object
	* @param template  $template  Template object
	* @param manager   $manager	  Data manager object
	*/
	public function __construct(container $container, driver $db, language $language, request $request, template $template, manager $manager)
	{
		$this->container = $container;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->manager = $manager;
		$this->template = $template;
	}

	/**
	* Display blocks
	*
	* @return void
	*/
	public function display_blocks(): void
	{
		// Add form key for form validation checks
		add_form_key('dls/blocks');

		$this->language->add_lang('acp_blocks', 'dls/web');

		// Get all blocks
		$sql = 'SELECT *
				FROM ' . $this->manager->blocks_data() . '
				ORDER BY block_id';
		$result = $this->db->sql_query($sql);

		$data_ary = $rowset = $count = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$count[$row['cat_name']]['block'] = (int) $row['position'];
			$count[$row['cat_name']]['position'][(int) $row['position']] = 0;
			if (!$count[$row['cat_name']]['block'])
			{
				$count[$row['cat_name']]['block']++;
			}

			if (!$count[$row['cat_name']]['position'][(int) $row['position']] && $row['active'])
			{
				$count[$row['cat_name']]['position'][(int) $row['position']]++;
			}

			$data_ary[$row['block_name']] = $row['ext_name'];
			$rowset[$row['cat_name']][] = [
				'cat_name'	 => $row['cat_name'],
				'block_name' => $row['block_name'],
				'ext_name'	 => $row['ext_name'],
				'active'	 => $row['active'],
				'position'	 => (int) $row['position'],
			];
		}
		$this->db->sql_freeresult($result);

		// Run check for available/unavailable blocks
		$this->check($data_ary, $count);

		$u_update = '';
		if ($s_status = $this->get_status())
		{
			$u_update = $s_status === 'add' ? array_column($this->status('add'), 'block_name') : $this->status($s_status);
		}

		// Is the form submitted
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('dls/blocks'))
			{
				trigger_error('FORM_INVALID');
			}

			// If the form has been submitted, set all data and save it
			$this->update_data($data_ary);

			$this->container->get('cache')->purge();

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_DLS_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set output vars for display in the template
		$this->assign_template_block_data($rowset, $count);

		unset($data_ary, $rowset, $count);

		// Set template vars
		$this->template->assign_vars([
			'S_UPDATE' => $s_status,
			'U_UPDATE' => $u_update,
			'U_ACTION' => $this->u_action,
		]);
	}

	/**
	* Update data
	*
	* @param array $data_ary Array of blocks data
	* @return void
	*/
	public function update_data(array $data_ary): void
	{
		foreach ($data_ary as $block_name => $ext_name)
		{
			$block_data = [
				'active'   => $this->request->variable($block_name, (int) 0),
				'position' => $this->request->variable($block_name . '_' . $ext_name, (int) 0),
			];

			// Update selected/requested block data
			$this->db->sql_query('UPDATE ' . $this->manager->blocks_data() . ' SET ' . $this->db->sql_build_array('UPDATE', $block_data) . "
				WHERE block_name = '" . $this->db->sql_escape($block_name) . "'"
			);

			// Purge removed block/service data from db. No confirm_box is needed! It is just a cleanup process :)
			if (in_array($block_name, $this->status('purge')))
			{
				$this->db->sql_query('DELETE FROM ' . $this->manager->blocks_data() . "
				WHERE block_name = '" . $this->db->sql_escape($block_name) . "'");
			}
		}

		// Add new blocks.. Will be removed
		if ($add_blocks = $this->status('add'))
		{
			$this->db->sql_multi_insert($this->manager->blocks_data(), $add_blocks);
		}
	}

	/**
	* Assign template data for blocks
	*
	* @param array $rowset Block data is stored here
	* @param array $count  Array of counted data [quantity of blocks and positions]
	* @return void
	*/
	protected function assign_template_block_data(array $rowset, array $count): void
	{
		foreach ($rowset as $category => $data)
		{
			// Set categories
			$this->template->assign_block_vars('category', ['cat_name' => strtoupper($category),]);

			// Add data to given categories
			foreach ($data as $block)
			{
				$this->template->assign_block_vars('category.block', [
					'name'		  => $block['block_name'],
					'position'	  => $block['block_name'] . '_' . $block['ext_name'],
					's_activate'  => $block['active'],
					'language'	  => strtoupper($block['block_name']),
					's_duplicate' => ($count[$category]['position'][$block['position']] > 1) && $block['active'] ?? false,
					's_options'	  => $count[$category]['block'],
					's_current'	  => $block['position'],
				]);
			}
		}
	}

	/**
	* Check conditioning
	*
	* @param array $block_data
	* @param array $count
	* @return void
	*/
	public function check(array $block_data, array $count): void
	{
		// Check for new blocks & prepare for installation
		$this->prepare($this->manager->get_new_blocks(), $count);

		// Check for unavailable blocks & prepare for purge
		foreach ($block_data as $service => $ext_name)
		{
			if (!$this->is_available(['block_name' => $service, 'ext_name' => $ext_name]))
			{
				// Set our block/service as unavailable
				$this->status['purge'][] = $service;
			}
		}
	}

	/**
	* Prepare data for installation
	*
	* @param array new_blocks
	* @param array $count
	* @return void
	*/
	protected function prepare(array $new_blocks, array $count): void
	{
		if (!$new_blocks)
		{
			return;
		}

		foreach ($new_blocks as $data)
		{
			$position = 1;
			if (array_key_exists($data['cat_name'], $count))
			{
				$position = end(array_keys($count[$data['cat_name']]['position']));
				$count[$data['cat_name']]['position'][] = $position++;
			}
			else if (in_array($data['cat_name'], array_column($this->status['add'], 'cat_name')))
			{
				$position++;
			}

			$this->status['add'][] = [
				'block_name' => $data['block_name'],
				'ext_name'	 => $data['ext_name'],
				'position'	 => $position,
				'active'	 => 0,
				'cat_name'	 => $data['cat_name'],
			];
		}
	}

	/**
	* Is service available
	*
	* @param array $row
	* @return bool
	*/
	protected function is_available(array $row): bool
	{
		return $this->container->has($this->manager->get_service($row['block_name'], $row['ext_name']));
	}

	/**
	* Get status
	*
	* @param string $status
	* @return array
	*/
	public function status(string $status): array
	{
		return $this->status[$status] ?? [];
	}

	/**
	* Check for update/purge status
	*
	* @return string
	*/
	public function get_status(): ?string
	{
		if (!$this->status)
		{
			return null;
		}
		else if ($this->status('add'))
		{
			$status = 'add';
		}
		else if ($this->status('purge'))
		{
			$status = 'purge';
		}

		return $status;
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return void
	*/
	public function set_page_url(string $u_action): void
	{
		$this->u_action = $u_action;
	}
}
