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

use ganstaz\gzo\src\helper\controller_helper;
use ganstaz\gzo\src\entity\manager as em;
use ganstaz\gzo\src\form\form;
use phpbb\config\config;
use phpbb\event\dispatcher;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\user;

/**
* Base controller class
*/
abstract class abstract_controller
{
	public function __construct(
		protected config $config,
		protected dispatcher $dispatcher,
		protected language $language,
		protected template $template,
		protected user $user,
		protected controller_helper $controller_helper,
		protected em $em,
		protected form $form,
		protected readonly string $root_path,
		protected readonly string $php_ext
	)
	{
	}
}
