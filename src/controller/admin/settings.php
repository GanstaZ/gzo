<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\controller\admin;

use ganstaz\web\model\admin\settings as sm;
use ganstaz\web\controller\abstract_controller;

/**
* Admin settings controller
*/
class settings extends abstract_controller
{
	/** @var sm */
	private object $sm;

	/**
	* Constructor
	*
	* @param sm $sm Sm object
	*/
	public function __construct($language, $request, $template, $em, $sm)
	{
		parent::__construct($language, $request, $template, $em);

		$this->sm = $sm;
	}

	/**
	* Handle controller
	*
	* @return void
	*/
	public function handle(): void
	{
		$this->language->add_lang('acp_web', 'ganstaz/web');

		$this->form->build($this->sm->data(), true);
		$this->form->add_form_key('ganstaz_web_settings');

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
