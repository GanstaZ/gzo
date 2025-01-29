<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\plugins\article;

use ganstaz\gzo\src\event\events;
use ganstaz\gzo\src\helper;
use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\controller\helper as controller;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\exception\http_exception;
use phpbb\language\language;
use phpbb\pagination;
use phpbb\template\template;
use phpbb\textformatter\s9e\renderer;
use phpbb\user;

class posts
{
	protected int $page = 0;
	public readonly array $breadcrumb;
	protected bool $trim_messages = false;
	protected bool $is_trimmed = false;
	protected string $news_order = 'p.post_id DESC';

	public function __construct
	(
		private auth $auth,
		private config $config,
		private driver_interface $db,
		private dispatcher $dispatcher,
		private controller $controller,
		private language $language,
		private pagination $pagination,
		private renderer $renderer,
		private template $template,
		private user $user,
		private helper $helper,
		private readonly string $root_path,
		private readonly string $php_ext
	)
	{
	}

	public function set_page_offset(int $page): self
	{
		$this->page = ($page - 1) * (int) $this->config['gzo_limit'];

		return $this;
	}

	public function set_breadcrumb_data(array $data): void
	{
		$this->breadcrumb = $data;
	}

	/**
	* Trim messages [Set to true if you want news to be trimmed]
	*/
	public function trim_messages(bool $bool): self
	{
		$this->trim_messages = $bool;

		return $this;
	}

	/**
	* News categories
	*/
	public function categories(int $fid): string
	{
		$sql_ary = [
			'SELECT' => 'forum_id, forum_name',
			'FROM'	 => [
				FORUMS_TABLE => 'f',
			],

			'WHERE'	 => 'forum_type = ' . FORUM_POST,
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql, 86400);

		$forum_ary = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$forum_ary[(int) $row['forum_id']] = (string) $row['forum_name'];
		}
		$this->db->sql_freeresult($result);

		return $forum_ary[$fid] ?? '';
	}

	/**
	* Articles base
	*/
	public function base(int $forum_id): void
	{
		$default = [(int) $this->config['gzo_main_fid'], (int) $this->config['gzo_news_fid'],];

		/** @event events::GZO_POSTS_ADD_CATEGORY */
		$vars = ['default'];
		extract($this->dispatcher->trigger_event(events::GZO_POSTS_ADD_CATEGORY, compact($vars)));

		$category_ids = $this->helper->get_forum_ids();

		// Check news id
		if (!in_array($forum_id, $category_ids) && !in_array($forum_id, $default))
		{
			throw new http_exception(404, 'NO_FORUM', [$forum_id]);
		}

		// Check permissions
		if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				throw new http_exception(403, 'SORRY_AUTH_READ', [$forum_id]);
			}

			login_box('', $this->language->lang('LOGIN_VIEWFORUM'));
		}

		$category = $this->categories($forum_id);

		// TODO: Change news to article
		// Assign breadcrumb
		$this->set_breadcrumb_data([
			$category, 'ganstaz_gzo_news', ['id' => $forum_id]
		]);

		$categories = [];
		foreach ($category_ids as $cid)
		{
			$categories[$this->categories($cid)] = $this->controller->route('ganstaz_gzo_news', ['id' => $cid]);
		}

		// Set template vars
		$this->template->assign_vars([
			'GZO_NEW_POST'		  => $this->controller->route('ganstaz_gzo_post_article', ['fid' => $forum_id]),
			'S_DISPLAY_POST_INFO' => $this->auth->acl_get('f_post', $forum_id) || $this->user->data['user_id'] === ANONYMOUS,
			'S_CATEGORIES'		  => $categories,
		]);

		// Do the sql thang
		$sql_ary = $this->get_sql_data($forum_id);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query_limit($sql, (int) $this->config['gzo_limit'], $this->page, 60);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('articles', $this->get_template_data($row));
		}
		$this->db->sql_freeresult($result);

		if ($this->config['gzo_pagination'] && null !== $this->page)
		{
			// Get total posts
			$sql_ary['SELECT'] = 'COUNT(p.post_id) AS num_posts';
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$total = (int) $this->db->sql_fetchfield('num_posts');
			$this->db->sql_freeresult($result);

			$base = [
				'routes' => [
					'ganstaz_gzo_news',
					'ganstaz_gzo_news_page',
				],
				'params' => ['id' => $forum_id],
			];

			$this->pagination->generate_template_pagination($base, 'pagination', 'page', $total, (int) $this->config['gzo_limit'], $this->page);

			$this->template->assign_var('total_news', $total);
		}
	}

	/**
	* Get sql data
	*/
	public function get_sql_data(int $id, string $where = 'forum'): array
	{
		$sql_where = 't.' . $where . '_id = ';

		$sql_ary = [
			'SELECT'	=> 't.topic_id, t.topic_title, t.topic_time, t.topic_views, t.topic_posts_approved,
			p.post_id, p.poster_id, p.post_text, u.user_id, u.username, u.user_posts, u.user_rank, u.user_colour, u.user_avatar,
			u.user_avatar_type, u.user_avatar_width, u.user_avatar_height',

			'FROM'		=> [
				TOPICS_TABLE => 't',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [POSTS_TABLE => 'p'],
					'ON'   => 'p.post_id = t.topic_first_post_id'
				],
				[
					'FROM' => [USERS_TABLE => 'u'],
					'ON'   => 'u.user_id = p.poster_id'
				],
			],

			'WHERE'		=> $sql_where . (int) $id . '
				AND t.topic_status <> ' . ITEM_MOVED . '
				AND t.topic_visibility = 1',
		];

		if ($where === 'forum')
		{
			$sql_ary['ORDER_BY'] = $this->news_order;
		}

		return $sql_ary;
	}

	/**
	* Get template data
	*/
	public function get_template_data(array $row): array
	{
		if (!function_exists('phpbb_get_user_rank'))
		{
			include("{$this->root_path}includes/functions_display.{$this->php_ext}");
		}

		$poster = [
			'user_rank'			 => $row['user_rank'],
			'user_avatar'		 => $row['user_avatar'],
			'user_avatar_type'	 => $row['user_avatar_type'],
			'user_avatar_width'	 => $row['user_avatar_width'],
			'user_avatar_height' => $row['user_avatar_height'],
		];

		$poster_id = (int) $row['user_id'];
		$rank = phpbb_get_user_rank($poster, $row['user_posts']);
		$text = $this->renderer->render($row['post_text']);

		return [
			'id'			  => $row['post_id'],
			'link'			  => $this->controller->route('ganstaz_gzo_article', ['aid' => $row['topic_id']]),
			'title'			  => $this->helper->truncate($row['topic_title'], $this->config['gzo_title_length']),
			'date'			  => $this->user->format_date($row['topic_time']),

			'author'		  => $poster_id,
			'author_name'	  => $row['username'],
			'author_color'	  => $row['user_colour'],
			'author_profile'  => $this->controller->route('ganstaz_gzo_member', ['username' => $row['username']]),

			'author_avatar'	  => [(array) $poster],
			'author_rank'	  => $rank['title'],
			'author_rank_img' => $rank['img'],
			'views'		 => $row['topic_views'],
			'replies'	 => $row['topic_posts_approved'] - 1,
			'text'		 => $this->trim_messages ? $this->trim_message($text) : $text,
			'is_trimmed' => $this->is_trimmed,
		];
	}

	/**
	* Trim message
	*/
	public function trim_message(string $text): string
	{
		$this->is_trimmed = false;

		if (utf8_strlen($text) > (int) $this->config['gzo_content_length'])
		{
			$this->is_trimmed = true;

			$offset = ((int) $this->config['gzo_content_length'] - 3) - utf8_strlen($text);
			$text	= utf8_substr($text, 0, utf8_strrpos($text, ' ', $offset));
		}

		return $text;
	}

	/**
	* Get forum id
	*/
	public function get_forum_id(int $topic_id): array
	{
		$sql = 'SELECT forum_id
				FROM ' . TOPICS_TABLE . '
				WHERE topic_id = ' . $topic_id;
		$result = $this->db->sql_query($sql, 3600);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row;
	}

	/**
	* Get first post (without any comments)
	*/
	public function get_first_post(int $topic_id): void
	{
		$sql_ary = $this->get_sql_data($topic_id, 'topic');
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql, 86400);
		$row = $this->db->sql_fetchrow($result);

		if (!$row)
		{
			throw new http_exception(404, 'NO_TOPICS', [$row]);
		}

		$template_data = $this->get_template_data($row);

		/** @event events::GZO_ARTICLE_MODIFY_TEMPLATE_DATA */
		$vars = ['template_data'];
		extract($this->dispatcher->trigger_event(events::GZO_ARTICLE_MODIFY_TEMPLATE_DATA, compact($vars)));

		// Assign breadcrumb data
		$this->set_breadcrumb_data([
			$template_data['title'], 'ganstaz_gzo_first_post', ['aid' => $topic_id]
		]);

		$this->template->assign_block_vars('article', $template_data);

		$this->db->sql_freeresult($result);
	}
}
