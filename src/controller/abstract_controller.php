<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\controller;

use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use ganstaz\web\core\entity\manager as em;
use ganstaz\web\core\form\form;

/**
* Abstract controller
*/
class abstract_controller
{
	/** @var language */
	protected object $language;

	/** @var request */
	protected object $request;

	/** @var template */
	protected object $template;

	/** @var em */
	protected object $em;

	/** @var form */
	protected object $form;

	/** @var string Custom form action */
	protected string $u_action;

	/**
	* Constructor
	*
	* @param language $language Language object
	* @param request  $request	Request object
	* @param template $template Template object
	* @param em		  $em		Entity manager object
	*/
	public function __construct(language $language, request $request, template $template, em $em)
	{
		$this->language = $language;
		$this->request	= $request;
		$this->template = $template;
		$this->em = $em;

		$this->form = new form($this->request, $this->template);
	}

	/**
	* Show user confirmation of success and provide link back to the previous screen
	*
	* @return bool
	*/
	public function settings_saved_message(): bool
	{
		return trigger_error($this->language->lang('ACP_GZO_SETTINGS_SAVED') . adm_back_link($this->u_action));
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
