<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\controller;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\language\language;
use ganstaz\web\model\posts;

/**
* GZ Web: base controller
*/
abstract class base
{
	/** @var config */
	protected $config;

	/** @var controller helper */
	protected $helper;

	/** @var language */
	protected $language;

	/** @var posts */
	protected $posts;

	/**
	* Constructor
	*
	* @param config   $config   Config object
	* @param helper	  $helper	Controller helper object
	* @param language $language Language object
	* @param posts    $posts    Posts object
	*/
	public function __construct(config $config, helper $helper, language $language, posts $posts)
	{
		$this->config   = $config;
		$this->helper	= $helper;
		$this->language = $language;
		$this->posts    = $posts;
	}
}
