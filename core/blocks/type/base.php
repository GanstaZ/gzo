<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\blocks\type;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\controller\helper as controller;
use phpbb\template\template;
use phpbb\event\dispatcher;

/**
* GZO Web: base class for blocks
*/
abstract class base implements block_interface
{
	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var controller helper */
	protected $controller;

	/** @var template */
	protected $template;

	/** @var dispatcher */
	protected $dispatcher;

	/** @var bool loading */
	protected $loading;

	/** @var root_path */
	protected $root_path;

	/** @var php_ext */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param config			  $config	  Config object
	* @param driver_interface $db		  Database object
	* @param controller		  $controller Controller helper object
	* @param template		  $template	  Template object
	* @param dispatcher		  $dispatcher Dispatcher object
	* @param string			  $root_path  Path to the phpbb includes directory
	* @param string			  $php_ext	  PHP file extension
	*/
	public function __construct(config $config, driver_interface $db, controller $controller, template $template, dispatcher $dispatcher, $root_path, $php_ext)
	{
		$this->config	  = $config;
		$this->db		  = $db;
		$this->controller = $controller;
		$this->dispatcher = $dispatcher;
		$this->template	  = $template;
		$this->root_path  = $root_path;
		$this->php_ext	  = $php_ext;
	}

	/**
	* {@inheritdoc}
	*/
	public function set_active(bool $set): void
	{
		$this->loading = $set;
	}

	/**
	* {@inheritdoc}
	*/
	public function is_load_active(): bool
	{
		return $this->loading;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_block_data(): array
	{
		return [];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
	}

	/**
	* Truncate title
	*
	* @param string		 $title	 Truncate title
	* @param int		 $length Max length of the string
	* @param null|string $ellips Language ellips
	* @return string
	*/
	public function truncate(string $title, int $length, $ellips = null): string
	{
		return truncate_string(censor_text($title), $length, 255, false, $ellips ?? '...');
	}
}
