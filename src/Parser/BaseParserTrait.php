<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser\Parser;

use ClayFreeman\LinkHeaderParser\Lexeme;
use ClayFreeman\LinkHeaderParser\LexemeStream;
use ClayFreeman\LinkHeaderParser\Token;

/**
 * A trait to define the internal base operations of our parser.
 *
 * This symbol is primarily used in other traits to ensure that certain
 * functionality can safely be assumed to be available.
 */
trait BaseParserTrait {

  /**
   * Consume a lexeme matching a specific token from the supplied stream.
   *
   * @param \ClayFreeman\LinkHeaderParser\LexemeStream $stream
   *   The lexeme stream from which to consume a specific token.
   * @param \ClayFreeman\LinkHeaderParser\Token $token
   *   The token that will satisfy the caller's expectation.
   *
   * @throws \UnexpectedValueException
   *   An exception describing a parse error in the lexeme stream.
   *
   * @return \ClayFreeman\LinkHeaderParser\Lexeme
   *   A lexeme matching the specified token.
   */
  protected function consume(LexemeStream $stream, Token $token): Lexeme {
    $this->expect($stream, [$token]);
    return $stream->consume();
  }

  /**
   * Ensure that the stream's next lexeme matches one of the supplied tokens.
   *
   * @param \ClayFreeman\LinkHeaderParser\LexemeStream $stream
   *   The lexeme stream from which to expect a token match.
   * @param \ClayFreeman\LinkHeaderParser\Token[] $tokens
   *   A list of tokens that will satisfy the caller's expectation.
   *
   * @throws \UnexpectedValueException
   *   An exception describing a parse error in the lexeme stream.
   */
  protected function expect(LexemeStream $stream, array $tokens): void {
    $tokens = array_unique($tokens);
    $next = $stream->peek();

    if (!in_array($next?->token, $tokens, TRUE)) {
      $subject = "{$next?->token->name} token" ?? 'end of stream';

      $expect = array_map(fn (Token $token) => $token->name, $tokens);
      $expect = implode(', ', $expect);

      throw new \UnexpectedValueException("Unexpected {$subject}; expecting token(s): {$expect}");
    }
  }

}
