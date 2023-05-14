<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser;

use ClayFreeman\LinkHeaderParser\Parser\ParameterListParserTrait;
use ClayFreeman\LinkHeaderParser\Parser\URIReferenceParserTrait;

use Psr\Http\Message\StreamInterface;

/**
 * Used to parse a HTTP 'Link' header value from a lexeme stream.
 */
class Parser {

  use ParameterListParserTrait;
  use URIReferenceParserTrait;

  /**
   * A HTTP 'Link' header value lexer.
   *
   * @var \ClayFreeman\LinkHeaderParser\Lexer
   */
  protected Lexer $lexer;

  /**
   * Constructs a Parser object.
   *
   * @param \ClayFreeman\LinkHeaderParser\Lexer $lexer
   *   A HTTP 'Link' header value lexer.
   */
  public function __construct(Lexer $lexer) {
    $this->lexer = $lexer;
  }

  /**
   * Parse the supplied input stream as a HTTP 'Link' header value.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream to parse.
   *
   * @return object
   *   An object representation of the parsed message.
   */
  public function parse(StreamInterface $input): object {
    $stream = $this->lexer->analyze($input);

    $result = (object) [
      'uri_reference' => $this->parseURIReference($stream),
      'parameters' => $this->parseParameters($stream),
    ];

    return $result;
  }

}
