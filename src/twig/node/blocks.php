<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\twig\node;

use phpbb\template\twig\environment;
use Twig\Node\Expression\AbstractExpression;
use Twig\Compiler;
use Twig\Node\Node;

class blocks extends Node
{
	public function __construct(
		AbstractExpression $expr,
		protected environment $environment,
		int $lineno
	)
	{
		parent::__construct(['expr' => $expr], [], $lineno);
	}

	/**
	* Compiles the node to PHP.
	*/
	public function compile(Compiler $compiler)
	{
		$compiler->addDebugInfo($this);

		$section = $this->getNode('expr')->getAttribute('name');

		foreach ($this->environment->get_gzo_blocks($section) as $name => $path)
		{
			$block = '@' . $path . '/block/' . $name . '.twig';

			if ($this->environment->isDebug() || $this->environment->getLoader()->exists($block))
			{
				$compiler
					->write("\$this->env->loadTemplate(\$this->env->getTemplateClass('$block'), '$block')->display(\$context);\n")
				;
			}
		}
	}
}
