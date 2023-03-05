<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\blocks\type;

use ganstaz\gzo\src\info;
use ganstaz\gzo\src\event\events;

/**
* Who's Online block
*/
class whos_online extends base
{
	/** @var info */
	protected $info;

	/**
	* Constructor
	*
	* @param info $info Forum info helper object
	*/
	public function __construct($config, $db, $controller, $template, $dispatcher, $helper, $root_path, $php_ext, info $info)
	{
		parent::__construct($config, $db, $controller, $template, $dispatcher, $helper, $root_path, $php_ext);

		$this->info = $info;
	}

	/**
	* {@inheritdoc}
	*/
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
	public function load(): void
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

		/** @event ganstaz.gzo.main_blocks_after */
		$this->dispatcher->dispatch(events::gzo_main_blocks_after);
	}
}
