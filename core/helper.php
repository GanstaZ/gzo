<?php
/**
*
* GZ Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core;

/**
* GZ Web: posts helper class
*/
class helper
{
	/**
	* Constructor
	*/
	public function __construct()
	{
	}

	/**
	* Truncate title
	*
	* @param string		 $title	 Truncate title
	* @param int		 $length Max length of the string
	* @param null|string $ellips Language ellips
	* @return string
	*/
	public function truncate(string $title, int $length, $ellips = null): string
	{
		return truncate_string(censor_text($title), $length, 255, false, $ellips ?? '...');
	}
}
