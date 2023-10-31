<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\twig\tokenparser;

use phpbb\template\twig\environment;

class event extends \Twig\TokenParser\AbstractTokenParser
{
	public function __construct(protected environment $environment)
	{
	}

	/**
	* Parses a token and returns a node.
	*/
	public function parse(\Twig\Token $token): \Twig\Node\Node
	{
		$expr = $this->parser->getExpressionParser()->parseExpression();

		$stream = $this->parser->getStream();
		$stream->expect(\Twig\Token::BLOCK_END_TYPE);

		return new \ganstaz\gzo\src\twig\node\event($expr, $this->environment, $token->getLine(), $this->getTag());
	}

	/**
	* Gets the tag name associated with this token parser.
	*/
	public function getTag(): string
	{
		return 'EVENT';
	}
}
