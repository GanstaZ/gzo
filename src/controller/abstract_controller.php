<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\controller;

use ganstaz\gzo\src\controller\helper;
use phpbb\request\request;
use ganstaz\gzo\src\entity\manager as em;
use ganstaz\gzo\src\form\form;

/**
* Abstract controller
*/
abstract class abstract_controller
{
	/** @var form */
	protected object $form;

	/** @var string Custom form action */
	protected string $u_action;

	public function __construct(
		protected helper $helper,
		protected request $request,
		protected em $em
	)
	{
		$this->form = new form($this->request, $this->helper->twig);
	}

	/**
	* Show user confirmation of success and provide link back to the previous screen
	*
	* @deprecated 2.4.0-a30
	* @return bool
	*/
	protected function settings_saved_message(): bool
	{
		return trigger_error($this->helper->language->lang('ACP_GZO_SETTINGS_SAVED') . adm_back_link($this->u_action));
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @deprecated 2.4.0-a30
	* @return self
	*/
	public function set_page_url(string $u_action): self
	{
		$this->u_action = $u_action;

		return $this;
	}
}
