<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\plugin\article;

use ganstaz\gzo\src\event\events;
use ganstaz\gzo\src\helper;
use ganstaz\gzo\src\plugin\plugin_base;
use ganstaz\gzo\src\user\loader as users_loader;
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

final class posts extends plugin_base
{
	protected int $page = 0;
	public readonly array $breadcrumb;
	protected bool $trim_messages = false;
	protected bool $is_trimmed = false;
	protected string $news_order = 'p.post_id DESC';

	public function __construct
	(
		config $config,
		controller $controller,
		driver_interface $db,
		dispatcher $dispatcher,
		template $template,
		users_loader $users_loader,
		$root_path,
		$php_ext,
		protected auth $auth,
		protected language $language,
		protected pagination $pagination,
		protected renderer $renderer,
		protected user $user,
		protected helper $helper
	)
	{
		parent::__construct($config, $controller, $db, $dispatcher, $template, $users_loader, $root_path, $php_ext);
	}

	public function set_page_offset(int $page): self
	{
		$this->page = ($page - 1) * (int) $this->config['gzo_limit'];

		return $this;
	}

	public function set_breadcrumb_data(string $name, string $route, array $params = []): void
	{
		$this->breadcrumb = [
			'name'	 => $name,
			'route'	 => $route,
			'params' => $params
		];
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
		$this->set_breadcrumb_data($category, 'ganstaz_gzo_news', ['id' => $forum_id]);

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

		// Pagination
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
	* @param int	$id	  Either forum or topic id
	* @param string $type By default it's forum, but could be topic
	*/
	public function get_sql_data(int $id, string $type = 'forum'): array
	{
		$sql_where = 't.' . $type . '_id = ' . $id;

		$sql_ary = [
			'SELECT'	=> 't.topic_id, t.topic_title, t.topic_time, t.topic_views, t.topic_posts_approved,
			p.post_id, p.poster_id, p.post_text',

			'FROM'		=> [
				TOPICS_TABLE => 't',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [POSTS_TABLE => 'p'],
					'ON'   => 'p.post_id = t.topic_first_post_id'
				],
			],

			'WHERE'		=> $sql_where . '
				AND t.topic_status <> ' . ITEM_MOVED . '
				AND t.topic_visibility = 1',
		];

		if ($type === 'forum')
		{
			$sql_ary['ORDER_BY'] = $this->news_order;
		}

		return $sql_ary;
	}

	/**
	* @param array $row Article data array
	*/
	public function get_template_data(array $row): array
	{
		$user_id = (int) $row['poster_id'];
		$user = $this->users_loader->get_user($user_id);
		$rank = $this->users_loader->get_rank_data($user);
		$text = $this->renderer->render($row['post_text']);

		return [
			'id'			  => $row['post_id'],
			'link'			  => $this->controller->route('ganstaz_gzo_article', ['aid' => $row['topic_id']]),
			'title'			  => $this->truncate($row['topic_title'], $this->config['gzo_title_length']),
			'date'			  => $this->user->format_date($row['topic_time']),

			'author'		  => $user_id,
			'author_name'	  => $user['username'],
			'author_color'	  => $user['user_colour'],
			'author_profile'  => $this->controller->route('ganstaz_gzo_member', ['username' => $user['username']]),
			'author_avatar'	  => [$this->users_loader->get_avatar_data($user_id)],
			'author_rank'	  => $rank['rank_title'],
			'author_rank_img' => $rank['rank_img'],

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
	public function get_forum_id(int $topic_id): string
	{
		$sql = 'SELECT forum_id
				FROM ' . TOPICS_TABLE . '
				WHERE topic_id = ' . $topic_id;
		$result = $this->db->sql_query($sql, 3600);
		$row = (int) $this->db->sql_fetchfield('forum_id');
		$this->db->sql_freeresult($result);

		return $row ?? '';
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
		$this->set_breadcrumb_data($template_data['title'], 'ganstaz_gzo_first_post', ['aid' => $topic_id]);

		$this->template->assign_block_vars('article', $template_data);

		$this->db->sql_freeresult($result);
	}
}
