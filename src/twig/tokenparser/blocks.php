<?php
/**
*
* GZO Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2022, GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\web\core\twig\tokenparser;

use phpbb\template\twig\environment;

class blocks extends \Twig\TokenParser\AbstractTokenParser
{
	/** @var environment */
	protected $environment;

	/**
	* Constructor
	*
	* @param environment $environment
	*/
	public function __construct(environment $environment)
	{
		$this->environment = $environment;
	}

	/**
	* Parses a token and returns a node.
	*
	* @param \Twig\Token $token A Twig\Token instance
	*
	* @return \Twig\Node\Node A Twig\Node instance
	*/
	public function parse(\Twig\Token $token)
	{
		$expr = $this->parser->getExpressionParser()->parseExpression();

		$stream = $this->parser->getStream();
		$stream->expect(\Twig\Token::BLOCK_END_TYPE);

		return new \ganstaz\web\core\twig\node\blocks($expr, $this->environment, $token->getLine(), $this->getTag());
	}

	/**
	* Gets the tag name associated with this token parser.
	*
	* @return string The tag name
	*/
	public function getTag()
	{
		return 'BLOCKS';
	}
}
