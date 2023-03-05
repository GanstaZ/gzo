<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\model;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher;
use phpbb\controller\helper as controller;
use phpbb\language\language;
use phpbb\pagination;
use phpbb\textformatter\s9e\renderer;
use phpbb\template\template;
use phpbb\user;
use ganstaz\gzo\src\helper;
use ganstaz\gzo\src\event\events;

/**
* Posts model
*/
class posts
{
	/** @var auth */
	protected $auth;

	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var dispatcher */
	protected $dispatcher;

	/** @var controller helper */
	protected $controller;

	/** @var language */
	protected $language;

	/** @var pagination */
	protected $pagination;

	/** @var s9e renderer */
	protected $renderer;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var root_path */
	protected $root_path;

	/** @var php_ext */
	protected $php_ext;

	/** @var helper */
	protected $helper;

	/** @var int Page offset for pagination */
	protected $page;

	/** @var bool enable trim */
	protected $trim_messages = false;

	/** @var bool is trimmed */
	protected $is_trimmed;

	/** @var string news order */
	protected $news_order = 'p.post_id DESC';

	/**
	* Constructor
	*
	* @param auth			  $auth		  Auth object
	* @param config			  $config	  Config object
	* @param driver_interface $db		  Database object
	* @param dispatcher		  $dispatcher Dispatcher object
	* @param controller		  $controller Controller helper object
	* @param language		  $language	  Language object
	* @param pagination		  $pagination Pagination object
	* @param renderer		  $renderer	  s9e renderer object
	* @param template		  $template	  Template object
	* @param user			  $user		  User object
	* @param helper			  $helper	  Posts helper object
	* @param string			  $root_path  Path to the phpbb includes directory
	* @param string			  $php_ext	  PHP file extension
	*/
	public function __construct
	(
		auth $auth,
		config $config,
		driver_interface $db,
		dispatcher $dispatcher,
		controller $controller,
		language $language,
		pagination $pagination,
		renderer $renderer,
		$template,
		user $user,
		helper $helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth		  = $auth;
		$this->config	  = $config;
		$this->db		  = $db;
		$this->dispatcher = $dispatcher;
		$this->controller = $controller;
		$this->language	  = $language;
		$this->pagination = $pagination;
		$this->renderer	  = $renderer;
		$this->template	  = $template;
		$this->user		  = $user;
		$this->helper	  = $helper;
		$this->root_path  = $root_path;
		$this->php_ext	  = $php_ext;
	}

	/**
	* Set page start
	*
	* @param int $page
	* @return self
	*/
	public function set_page(int $page): self
	{
		$this->page = ($page - 1) * (int) $this->config['gzo_limit'];

		return $this;
	}

	/**
	* Trim messages [Set to true if you want news to be trimmed]
	*
	* @param bool $bool
	* @return self
	*/
	public function trim_messages(bool $bool): self
	{
		$this->trim_messages = $bool;

		return $this;
	}

	/**
	* Assign breadcrumb
	*
	* @param string $name	Name of the breadcrumb
	* @param string $route	Name of the route
	* @param array	$params Additional params
	* @return self
	*/
	public function assign_breadcrumb(string $name, string $route, array $params): self
	{
		$this->template->assign_block_vars('navlinks', [
			'FORUM_NAME'   => $name,
			'U_VIEW_FORUM' => $this->controller->route($route, $params),
		]);

		return $this;
	}

	/**
	* News categories
	*
	* @param int $fid
	* @return string
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
	*
	* @param int $forum_id Forum id to fetch news data
	* @return void
	*/
	public function base(int $forum_id): void
	{
		$default = [(int) $this->config['gzo_main_fid'], (int) $this->config['gzo_news_fid'],];

		/** @event ganstaz.gzo.posts_add_category */
		$vars = ['default'];
		extract($this->dispatcher->trigger_event(events::gzo_posts_add_category, compact($vars)));

		$category_ids = $this->helper->get_forum_ids();

		// Check news id
		if (!in_array($forum_id, $category_ids) && !in_array($forum_id, $default))
		{
			throw new \phpbb\exception\http_exception(404, 'NO_FORUM', [$forum_id]);
		}

		// Check permissions
		if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				throw new \phpbb\exception\http_exception(403, 'SORRY_AUTH_READ', [$forum_id]);
			}

			login_box('', $this->language->lang('LOGIN_VIEWFORUM'));
		}

		$category = $this->categories($forum_id);

		// Assign breadcrumb
		$this->assign_breadcrumb($category, 'ganstaz_gzo_news', ['id' => $forum_id]);

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
			$this->template->assign_block_vars('news', $this->get_template_data($row));
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
	*
	* @param int	$id	   id to get news or article data
	* @param string $where query where clause [forum or topic]
	* @return array
	*/
	public function get_sql_data(int $id, string $where = 'forum'): array
	{
		$sql_where = 't.' . $where . '_id = ';

		$sql_ary = [
			'SELECT'	=> 't.topic_id, t.forum_id, t.topic_title, t.topic_time, t.topic_views, t.topic_status, t.topic_posts_approved,
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
	*
	* @param array $row data array
	* @return array
	*/
	public function get_template_data(array $row): array
	{
		if (!function_exists('phpbb_get_user_rank'))
		{
			include("{$this->root_path}includes/functions_display.{$this->php_ext}");
		}

		$poster = [
			'user_rank'		=> $row['user_rank'],
			'avatar'		=> $row['user_avatar'],
			'avatar_type'	=> $row['user_avatar_type'],
			'avatar_width'	=> $row['user_avatar_width'],
			'avatar_height'	=> $row['user_avatar_height'],
		];

		$rank_title = phpbb_get_user_rank($poster, $row['user_posts']);
		$text = $this->renderer->render($row['post_text']);

		return [
			'id'		 => $row['post_id'],
			'link'		 => $this->controller->route('ganstaz_gzo_article', ['aid' => $row['topic_id']]),
			'title'		 => $this->helper->truncate($row['topic_title'], $this->config['gzo_title_length']),
			'date'		 => $this->user->format_date($row['topic_time']),
			'author'	 => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
			'avatar'	 => phpbb_get_user_avatar($poster),
			'rank'		 => $rank_title['title'],
			'views'		 => $row['topic_views'],
			'replies'	 => $row['topic_posts_approved'] - 1,
			'text'		 => $this->trim_messages ? $this->trim_message($text) : $text,
			'is_trimmed' => $this->is_trimmed,
		];
	}

	/**
	* Trim message
	*
	* @param string $text
	* @return string
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
	*
	* @param int $topic_id the id of the article
	* @return array
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
	*
	* @param int $topic_id the id of the article
	* @return void
	*/
	public function get_first_post(int $topic_id): void
	{
		// Do the sql thang
		$sql_ary = $this->get_sql_data($topic_id, 'topic');
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql, 86400);
		$row = $this->db->sql_fetchrow($result);

		if (!$row)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_TOPICS', [$row]);
		}

		$template_data = $this->get_template_data($row);

		/** @event ganstaz.gzo.article_modify_template_data */
		$vars = ['template_data'];
		extract($this->dispatcher->trigger_event(events::gzo_article_modify_template_data, compact($vars)));

		// Assign breadcrumb
		$this->assign_breadcrumb($template_data['title'], 'ganstaz_gzo_first_post', ['aid' => $topic_id]);

		$this->template->assign_block_vars('article', $template_data);

		$this->db->sql_freeresult($result);
	}
}
