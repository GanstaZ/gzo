<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\twig;

use phpbb\template\twig\environment;
use phpbb\group\helper as group;

class extension extends \Twig\Extension\AbstractExtension
{
	public function __construct(
		protected environment $environment,
		protected group $group
	)
	{
	}

	/**
	* Returns the token parser instance to add to the existing list.
	*/
	public function getTokenParsers(): array
	{
		return [
			new \ganstaz\gzo\src\twig\tokenparser\blocks($this->environment),
		];
	}

	/**
	* Returns a list of global functions to add to the existing list.
	*/
	public function getFunctions(): array
	{
		return [
			new \Twig\TwigFunction('blocks', [$this, 'load_blocks'], ['needs_environment' => true, 'needs_context' => true]),
			new \Twig\TwigFunction('link', [$this, 'link'], ['needs_environment' => true]),
			new \Twig\TwigFunction('get_group_name', [$this, 'get_group_name']),
		];
	}

	public function load_blocks(environment $environment, $context, string $section): void
	{
		foreach ($environment->gzo_get_blocks($section) as $name => $path)
		{
			$block = '@' . $path . '/block/' . $name . '.twig';

			if ($environment->getLoader()->exists($block))
			{
				$environment->loadTemplate($environment->getTemplateClass($block), $block)->display($context);
			}
		}
	}

	public function link(environment $environment, array $attributes = [], string $text = ''): string
	{
		try
		{
			return $environment->render('@ganstaz_gzo/macros/link.twig', [
				'attributes' => (array) $attributes,
				'text'       => (string) $text
			]);
		}
		catch (\Twig\Error\Error $e)
		{
			return $e->getMessage();
		}
	}

	public function get_group_name(string $group_name): string
	{
		return $this->group->get_name($group_name);
	}
}
