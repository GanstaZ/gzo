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

	/** @var root_path */
	protected $root_path;

	/** @var php_ext */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param config   $config    Config object
	* @param helper	  $helper	 Controller helper object
	* @param language $language  Language object
	* @param posts    $posts     Posts object
	* @param string	  $root_path Path to the phpbb includes directory
	* @param string	  $php_ext   PHP file extension
	*/
	public function __construct(config $config, helper $helper, language $language, posts $posts, $root_path, $php_ext)
	{
		$this->config    = $config;
		$this->helper	 = $helper;
		$this->language  = $language;
		$this->posts     = $posts;
		$this->root_path = $root_path;
		$this->php_ext   = $php_ext;
	}
}
