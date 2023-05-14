<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser\Lexer;

use ClayFreeman\LinkHeaderParser\Lexeme;
use ClayFreeman\LinkHeaderParser\Token;

use Psr\Http\Message\StreamInterface;

/**
 * A trait used to perform lexical analysis on a 'Link' header URI Reference.
 */
trait URIReferenceAnalysisTrait {

  use BaseLexerTrait;

  /**
   * Generate a lexeme representing a URI Reference.
   *
   * The URI Reference occurs first in the 'Link' header field value, surrounded
   * by angled brackets on either side, and optional whitespace.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\LinkHeaderParser\Lexeme[]
   *   A lexeme representing the URI Reference.
   */
  protected function analyzeURIReference(StreamInterface $input): \Generator {
    // Consume all leading whitespace (if present).
    //
    // This should already be taken care of by the upstream HTTP response
    // parser, but we perform this duty here as a courtesy in the case that this
    // library is supplied improperly sanitized header values.
    $this->read($input, "\t ");

    // The URI Reference is surrounded by angled brackets on either side.
    // Consume the leading left angle bracket.
    $this->read($input, '<', 'expecting a left angle bracket to delimit the start of a URI Reference');

    // Continue reading until we encounter a closing right angle bracket.
    //
    // The portion of the input stream consumed by this operation represents the
    // URI Reference value of the HTTP 'Link' header.
    $result = $this->readUntil($input, '>', 'expecting a URI Reference');

    // The URI Reference is surrounded by angled brackets on either side.
    // Consume the trailing right angle bracket.
    $this->read($input, '>', 'expecting a right angle bracket to delimit the end of a URI Reference');

    // Consume all trailing whitespace (if present).
    $this->read($input, "\t ");

    yield new Lexeme(Token::URIReference, $result);
  }

}
