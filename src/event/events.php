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
	/** Area base class */
	public const GZO_AREA_MODIFY_NAVIGATION = 'ganstaz.gzo.area_modify_navigation';

	/** Information block */
	public const GZO_INFORMATION_BEFORE = 'ganstaz.gzo.information_before';

	/** Who's Online block */
	public const GZO_MAIN_BLOCKS_AFTER = 'ganstaz.gzo.main_blocks_after';

	/** Admin blocks controller */
	public const GZO_ADMIN_BLOCK_ADD_LANGUAGE = 'ganstaz.gzo.admin_block_add_language';

	/** Posts model */
	public const GZO_POSTS_ADD_CATEGORY = 'ganstaz.gzo.posts_add_category';

	/** Posts model */
	public const GZO_ARTICLE_MODIFY_TEMPLATE_DATA = 'ganstaz.gzo.article_modify_template_data';
}
