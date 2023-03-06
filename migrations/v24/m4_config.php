<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\migrations\v24;

class m4_config extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	static public function depends_on()
	{
		return ['\ganstaz\gzo\migrations\v24\m1_main'];
	}

	/**
	* Add the initial data in the database
	*
	* @return array Array of table data
	* @access public
	*/
	public function update_data()
	{
		return [
			// Add the config variables we want to be able to set
			['config.add', ['gzo_core_version', '2.4.0-dev']],
			['config.add', ['gzo_main_fid', 2]],
			['config.add', ['gzo_news_fid', 3]],
			['config.add', ['gzo_news_link', 1]],
			['config.add', ['gzo_the_team_fid', 8]],
			['config.add', ['gzo_top_posters_fid', 0]],
			['config.add', ['gzo_recent_posts_fid', 0]],
			['config.add', ['gzo_recent_topics_fid', 0]],
			['config.add', ['gzo_profile_tabs', 0]],
			['config.add', ['gzo_pagination', 1]],
			['config.add', ['gzo_title_length', 26]],
			['config.add', ['gzo_content_length', 150]],
			['config.add', ['gzo_limit', 5]],
			['config.add', ['gzo_user_limit', 5]],
			// Blocks
			['config.add', ['gzo_blocks', 1]],
			['config.add', ['gzo_right', 1]],
			['config.add', ['gzo_left', 0]],
			['config.add', ['gzo_middle', 1]],
			['config.add', ['gzo_top', 0]],
			['config.add', ['gzo_bottom', 0]],
		];
	}
}
