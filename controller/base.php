<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\controller;

use phpbb\controller\helper;
use phpbb\language\language;
use ganstaz\web\model\page\news;

/**
* GZ Web: base controller
*/
abstract class base
{
	/** @var controller helper */
	protected $helper;

	/** @var language */
	protected $language;

	/** @var news */
	protected $news;

	/**
	* Constructor
	*
	* @param helper	  $helper	Controller helper object
	* @param language $language Language object
	* @param news     $news     News object
	*/
	public function __construct(helper $helper, language $language, news $news)
	{
		$this->helper	= $helper;
		$this->language = $language;
		$this->news     = $news;
	}
}
