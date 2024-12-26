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
use Twig\TokenParser\AbstractTokenParser;
use Twig\Node\Node;
use Twig\Token;

class blocks extends AbstractTokenParser
{
	public function __construct(protected environment $environment)
	{
	}

	/**
	* Parses a token and returns a node.
	*/
	public function parse(Token $token): Node
	{
		$expr = $this->parser->getExpressionParser()->parseExpression();

		$stream = $this->parser->getStream();
		$stream->expect(Token::BLOCK_END_TYPE);

		return new \ganstaz\gzo\src\twig\node\blocks($expr, $this->environment, $token->getLine());
	}

	/**
	* Gets the tag name associated with this token parser.
	*/
	public function getTag(): string
	{
		return 'BLOCKS';
	}
}
