<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\migrations\v24;

class m4_config extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	static public function depends_on()
	{
		return ['\ganstaz\web\migrations\v24\m1_main'];
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
			['config.add', ['gz_core_version', '2.4.0-dev']],
			['config.add', ['gz_main_fid', 2]],
			['config.add', ['gz_news_fid', 3]],
			['config.add', ['gz_the_team_fid', 8]],
			['config.add', ['gz_top_posters_fid', 0]],
			['config.add', ['gz_recent_posts_fid', 0]],
			['config.add', ['gz_recent_topics_fid', 0]],
			['config.add', ['gz_profile_tabs', 1]],
			['config.add', ['gz_pagination', 1]],
			['config.add', ['gz_title_length', 26]],
			['config.add', ['gz_content_length', 150]],
			['config.add', ['gz_limit', 5]],
			['config.add', ['gz_user_limit', 5]],
			// Blocks
			['config.add', ['gz_blocks', 1]],
			['config.add', ['gz_special', 1]],
			['config.add', ['gz_right', 1]],
			['config.add', ['gz_left', 0]],
			['config.add', ['gz_middle', 1]],
			['config.add', ['gz_top', 0]],
			['config.add', ['gz_bottom', 0]],
		];
	}
}
