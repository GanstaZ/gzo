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

use phpbb\config\config;
use phpbb\event\dispatcher_interface;
use phpbb\filesystem\filesystem;
use phpbb\path_helper;
use phpbb\extension\manager;
use phpbb\template\assets_bag;
use phpbb\template\twig\environment;

use Twig\Loader\LoaderInterface;
use ganstaz\gzo\src\blocks\data;

class phpbb_environment extends environment
{
	public function __construct(
		protected data $data,
		assets_bag $assets_bag,
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
		parent::__construct($assets_bag, $phpbb_config, $filesystem, $path_helper, $cache_path, $extension_manager, $loader, $phpbb_dispatcher, $options);
	}

	public function get_gzo_blocks(string $section): array
	{
		return $this->data->get($section);
	}
}
