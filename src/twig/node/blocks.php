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

class blocks extends \Twig\Node\Node
{
	public function __construct(
		AbstractExpression $expr,
		protected environment $environment,
		int $lineno,
		?string $tag = null
	)
	{
		parent::__construct(['expr' => $expr], [], $lineno, $tag);
	}

	/**
	* Compiles the node to PHP.
	*/
	public function compile(\Twig\Compiler $compiler)
	{
		$compiler->addDebugInfo($this);

		$section = $this->getNode('expr')->getAttribute('name');

		foreach ($this->environment->get_gzo_blocks($section) as $name => $path)
		{
			$block = '@' . $path . '/block/' . $name . '.twig';

			if ($this->environment->isDebug() || $this->environment->getLoader()->exists($block))
			{
				$compiler
					->write("\$this->env->loadTemplate('$block')->display(\$context);\n")
				;
			}
		}
	}
}
