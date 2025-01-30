<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\plugin\blocks;

use ganstaz\gzo\src\event\events;
use ganstaz\gzo\src\info;
use ganstaz\gzo\src\plugin\plugin_base;
use ganstaz\gzo\src\user\loader as users_loader;
use phpbb\config\config;
use phpbb\controller\helper as controller;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\template\template;

class whos_online extends plugin_base
{
	public function __construct(
		config $config,
		controller $controller,
		driver_interface $db,
		dispatcher $dispatcher,
		template $template,
		users_loader $users_loader,
		$root_path,
		$php_ext,

		private info $info
	)
	{
		parent::__construct($config, $controller, $db, $dispatcher, $template, $users_loader, $root_path, $php_ext);
	}

	public function get_block_data(): array
	{
		return [
			'section'  => 'gzo_bottom',
			'ext_name' => 'ganstaz_gzo',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load_plugin(): void
	{
		$total_posts  = (int) $this->config['num_posts'];
		$total_topics = (int) $this->config['num_topics'];
		$total_users  = (int) $this->config['num_users'];

		$boarddays = (time() - $this->config['board_startdate']) / 86400;

		$posts_per_day	= sprintf('%.2f', $total_posts / $boarddays);
		$topics_per_day = sprintf('%.2f', $total_topics / $boarddays);
		$users_per_day	= sprintf('%.2f', $total_users / $boarddays);

		// Generate birthday list if required...
		if ($this->info->show_birthdays())
		{
			$this->info->birthdays();
		}

		$this->info->legend();

		$this->template->assign_vars([
			'TOTAL_POSTS'  => $total_posts,
			'TOTAL_TOPICS' => $total_topics,
			'TOTAL_USERS'  => $total_users,
			'NEWEST_USER'  => get_username_string('full', (int) $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour']),

			'ppd' => $posts_per_day,
			'tpd' => $topics_per_day,
			'upd' => $users_per_day,
			'S_DISPLAY_BIRTHDAY_LIST' => $this->info->show_birthdays(),
		]);

		/** events::GZO_MAIN_BLOCKS_AFTER */
		$this->dispatcher->trigger_event(events::GZO_MAIN_BLOCKS_AFTER);
	}
}
