<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser\Parser;

use ClayFreeman\LinkHeaderParser\LexemeStream;
use ClayFreeman\LinkHeaderParser\Token;

/**
 * A trait used to parse a URI Reference.
 */
trait URIReferenceParserTrait {

  use BaseParserTrait;

  /**
   * Parse a URI Reference from the supplied stream.
   *
   * @param \ClayFreeman\LinkHeaderParser\LexemeStream $stream
   *   The lexeme stream from which to parse a URI Reference.
   *
   * @return string
   *   The parsed URI reference.
   */
  protected function parseURIReference(LexemeStream $stream): string {
    return $this->consume($stream, Token::URIReference)->value;
  }

}
