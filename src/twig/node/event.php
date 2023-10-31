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

class event extends \Twig\Node\Node
{
	protected string $listener_directory = 'event/';

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

		$location = $this->listener_directory . $this->getNode('expr')->getAttribute('name');

		foreach ($this->environment->get_phpbb_extensions() as $ext_namespace => $ext_path)
		{
			$ext_namespace = str_replace('/', '_', $ext_namespace);
			$event_file = '@' . $ext_namespace . '/' . $location . '.twig';

			if ($this->environment->isDebug())
			{
				// If debug mode is enabled, lets check for new/removed EVENT
				//  templates on page load rather than at compile. This is
				//  slower, but makes developing extensions easier (no need to
				//  purge the cache when a new event template file is added)
				$compiler
					->write("if (\$this->env->getLoader()->exists('$event_file')) {\n")
					->indent()
				;
			}

			if ($this->environment->isDebug() || $this->environment->getLoader()->exists($event_file))
			{
				$compiler
					->write("\$previous_look_up_order = \$this->env->getNamespaceLookUpOrder();\n")

					// We set the namespace lookup order to be this extension first, then the main path
					->write("\$this->env->setNamespaceLookUpOrder(['{$ext_namespace}', '__main__']);\n")
					->write("\$this->env->loadTemplate('$event_file')->display(\$context);\n")
					->write("\$this->env->setNamespaceLookUpOrder(\$previous_look_up_order);\n")
				;
			}

			if ($this->environment->isDebug())
			{
				$compiler
					->outdent()
					->write("}\n\n")
				;
			}
		}
	}
}
