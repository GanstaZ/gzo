<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\model\admin;

use ganstaz\web\core\helper;

/**
* Admin settings model
*/
class settings
{
	/** @var helper */
	private object $helper;

	/**
	* Constructor
	*
	* @param helper $helper Helper object
	*/
	public function __construct(helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	* Entity data
	*
	* @return array
	*/
	public function data(): array
	{
		return [
			'gzo_main_fid' => [
				'type'	   => 'choice',
				'options'  => [
					'choices' => $this->helper->get_forum_ids(),
					'custom'  => $this->s_forum_ids(),
				],
			],
			'gzo_news_fid' => [
				'type'	   => 'choice',
				'options'  => [
					'choices' => $this->helper->get_forum_ids(),
					'custom'  => $this->s_forum_ids(),
				],
			],
			'gzo_news_link' => [
				'type'		=> 'radio',
				'options'	=> [
					'values' => [1 => 'ENABLED', 0 => 'DISABLED'],
				],
			],
			'gzo_profile_tabs' => [
				'type'		   => 'radio',
				'options'	=> [
					'values' => [1 => 'ENABLED', 0 => 'DISABLED'],
				],
			],
			'gzo_pagination' => [
				'type'		 => 'radio',
				'options'	=> [
					'values' => [1 => 'ENABLED', 0 => 'DISABLED'],
				],
			],
			'gzo_user_limit' => [
				'type'		 => 'number',
				'options'	 => [
					'attributes' => ['min' => 1, 'max' => 10],
				],
			],
			'gzo_limit'	  => [
				'type'	  => 'number',
				'options' => [
					'attributes' => ['min' => 1, 'max' => 10],
				],
			],
			'gzo_title_length' => [
				'type'		   => 'number',
				'options'	   => [
					'attributes' => ['min' => 1, 'max' => 50],
				],
			],
			'gzo_content_length' => [
				'type'			 => 'number',
				'options'		 => [
					'attributes' => ['min' => 1, 'max' => 250],
				],
			],
			'gzo_blocks'  => [
				'type'	  => 'radio',
				'options' => [
					'values' => [1 => 'ENABLED', 0 => 'DISABLED'],
					'explain' => true,
					'group' => 'block',
				],
			],
			'gzo_right'		=> [
				'type'		=> 'radio',
				'options'	=> [
					'values' => [1 => 'ENABLED', 0 => 'DISABLED'],
					'group' => 'block',
				],
			],
			'gzo_left'		=> [
				'type'		=> 'radio',
				'options'	=> [
					'values' => [1 => 'ENABLED', 0 => 'DISABLED'],
					'group' => 'block',
				],
			],
			'gzo_middle'	=> [
				'type'		=> 'radio',
				'options'	=> [
					'values' => [1 => 'ENABLED', 0 => 'DISABLED'],
					'group' => 'block',
				],
			],
			'gzo_top'		=> [
				'type'		=> 'radio',
				'options'	=> [
					'values' => [1 => 'ENABLED', 0 => 'DISABLED'],
					'group' => 'block',
				],
			],
			'gzo_bottom'	=> [
				'type'		=> 'radio',
				'options'	=> [
					'values' => [1 => 'ENABLED', 0 => 'DISABLED'],
					'group' => 'block',
				],
			],
		];
	}

	public function s_forum_ids(): bool
	{
		return count($this->helper->get_forum_ids()) > 1;
	}
}
