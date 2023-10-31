<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\twig;

use phpbb\template\twig\environment;
use phpbb\config\config;
use phpbb\filesystem\filesystem;
use phpbb\path_helper;
use phpbb\extension\manager;
use Twig\Loader\LoaderInterface;
use phpbb\event\dispatcher_interface;
use ganstaz\gzo\src\blocks\event;

class phpbb_environment extends environment
{
	public function __construct(
		protected event $event,
		config $phpbb_config,
		filesystem $filesystem,
		path_helper $path_helper,
		string $cache_path,
		manager $extension_manager = null,
		LoaderInterface $loader = null,
		dispatcher_interface $phpbb_dispatcher = null,
		array $options = []
	)
	{
		parent::__construct($phpbb_config, $filesystem, $path_helper, $cache_path, $extension_manager, $loader, $phpbb_dispatcher, $options);
	}

	public function get_gzo_blocks(string $section): array
	{
		return $this->event->get($section);
	}
}
