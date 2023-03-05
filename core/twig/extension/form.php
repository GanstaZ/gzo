<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\twig\extension;

use phpbb\template\twig\environment;

/**
* Twig form extension
*/
class form extends \Twig\Extension\AbstractExtension
{
	/**
	* Returns a list of functions to add to the existing list.
	*
	* @return \Twig\TwigFunction[]			Array of twig functions
	*/
	public function getFunctions()
	{
		return [
			new \Twig\TwigFunction('form_widget', [$this, 'form_widget'], ['needs_environment' => true]),
		];
	}

	/**
	* Form widget
	*
	* @param environment $environment Twig environment object
	* @param array       $form_data
	* @return void
	*/
	public function form_widget(environment $env, array $form_data): void
	{
		if (!$form_data)
		{
			return;
		}

		foreach ($form_data as $row)
		{
			$s_custom = $row['OPTIONS']['s_custom'];
			$effix = is_bool($s_custom) && $s_custom === false ? '_custom' : '';
			$type = $row['type'] . $effix;

			if ($env->getLoader()->exists('@ganstaz_web/macros/form/' . $type . '.twig'))
			{
				$env->loadTemplate('@ganstaz_web/macros/form/' . $type . '.twig')->display($row);
			}
		}
	}
}
