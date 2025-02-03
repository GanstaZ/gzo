<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\migrations\v40;

use ganstaz\gzo\src\enum\gzo;

class m5_config extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc}
	 */
	public static function depends_on(): array
	{
		return [gzo::MAIN_MIGRATION];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data(): array
	{
		return [
			['config.add', ['gzo_main_fid', 2]],

			['config.add', ['gzo_news_fid', 3]],
			['config.add', ['gzo_news_link', 1]],

			['config.add', ['gzo_pagination', 1]],
			['config.add', ['gzo_title_length', 26]],
			['config.add', ['gzo_content_length', 150]],

			['config.add', ['gzo_limit', 5]],
			['config.add', ['gzo_users_per_list', 10]],

			['config.add', ['gzo_app_global', false]],
			// Plugins
			['config.add', ['gzo_the_team_fid', 8]],
			['config.add', ['gzo_top_posters_fid', 0]],
			['config.add', ['gzo_recent_posts_fid', 0]],
			['config.add', ['gzo_recent_topics_fid', 0]],

			['config.add', ['gzo_plugins', 1]],
			['config.add', [gzo::SECTION['side'], true]],
			['config.add', [gzo::SECTION['top'], true]],
			['config.add', [gzo::SECTION['bottom'], true]],
			['config.add', [gzo::SECTION['block'], true]],
		];
	}
}
