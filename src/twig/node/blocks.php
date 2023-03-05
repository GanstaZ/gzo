<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\twig\node;

class blocks extends \Twig\Node\Node
{
	/** @var \Twig\Environment */
	protected $environment;

	public function __construct(\Twig\Node\Expression\AbstractExpression $expr, \phpbb\template\twig\environment $environment, $lineno, $tag = null)
	{
		$this->environment = $environment;

		parent::__construct(['expr' => $expr], [], $lineno, $tag);
	}

	/**
	* Compiles the node to PHP.
	*
	* @param \Twig\Compiler A Twig\Compiler instance
	*/
	public function compile(\Twig\Compiler $compiler)
	{
		$compiler->addDebugInfo($this);

		$block = $this->getNode('expr')->getAttribute('name');
		$block_loader = $this->environment->getExtension('ganstaz\web\core\twig\extension')->get_block_loader($block);

		foreach ($block_loader as $name => $path)
		{
			$path = $path . '/block';

			if ($this->environment->isDebug() || $this->environment->getLoader()->exists("@{$path}/{$name}.twig"))
			{
				$compiler
					->write("\$this->env->loadTemplate('@{$path}/{$name}.twig')->display(\$context);\n")
				;
			}
		}
	}
}