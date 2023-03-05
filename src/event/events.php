<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\event;

/**
* Event
*/
final class events
{
	/** Information block */
	public const gzo_information_before = 'ganstaz.gzo.information_before';

	/** Who's Online block */
	public const gzo_main_blocks_after = 'ganstaz.gzo.main_blocks_after';

	/** Admin blocks controller */
	public const gzo_admin_block_add_language = 'ganstaz.gzo.admin_block_add_language';

	/** Posts model */
	public const gzo_posts_add_category = 'ganstaz.gzo.posts_add_category';

	/** Posts model */
	public const gzo_article_modify_template_data = 'ganstaz.gzo.article_modify_template_data';
}
