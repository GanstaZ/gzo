<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\controller\admin;

use phpbb\event\dispatcher;
use ganstaz\gzo\src\controller\helper;
use ganstaz\gzo\src\entity\manager as em;
use ganstaz\gzo\src\form\form;
use ganstaz\gzo\src\model\admin\settings as sm;
use ganstaz\gzo\src\controller\abstract_controller;

class settings extends abstract_controller
{
	public function __construct(
		dispatcher $dispatcher,
		helper $helper,
		em $em,
		form $form,
		$root_path,
		$php_ext,
		private readonly sm $sm
	)
	{
		parent::__construct($dispatcher, $helper, $em, $form, $root_path, $php_ext);
	}

	/**
	* Handle controller
	*/
	public function handle(): void
	{
		$this->helper->language->add_lang('acp_gzo', 'ganstaz/gzo');

		$this->form->build($this->sm->data(), true);
		$this->form->add_form_key('ganstaz_gzo_settings');

		$emc = $this->em->type('config');

		// Testing
		//var_dump($this->form->config());
		//var_dump($this->form->_get('common'));
		var_dump($this->form->err());
		//var_dump($this->em->available());

		if ($this->form->is_submitted() && $this->form->is_valid())
		{
			if ($this->sm->s_forum_ids())
			{
				$emc->set($this->form->_get('special'));
			}

			$emc->set($this->form->_get('common'));

			$this->settings_saved_message();
		}

		$this->form->create_view($this->u_action);
	}
}
