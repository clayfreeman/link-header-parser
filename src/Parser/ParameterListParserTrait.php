<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser\Parser;

use ClayFreeman\LinkHeaderParser\LexemeStream;
use ClayFreeman\LinkHeaderParser\Parser\Value\Parameter;
use ClayFreeman\LinkHeaderParser\Token;

/**
 * A trait used to parse a parameter list.
 */
trait ParameterListParserTrait {

  use BaseParserTrait;

  /**
   * Parse a parameter from the supplied stream.
   *
   * This method expects that the presence of a parameter has been established
   * prior to being invoked. This can be done by ensuring that the next lexeme
   * token represents a parameter name.
   *
   * @param \ClayFreeman\LinkHeaderParser\LexemeStream $stream
   *   The lexeme stream from which to parse a parameter.
   *
   * @return \ClayFreeman\LinkHeaderParser\Parser\Value\Parameter
   *   A parameter parsed from the lexeme stream.
   */
  private function parseParameter(LexemeStream $stream): Parameter {
    $name = $this->consume($stream, Token::ParameterName)->value;
    $value = '';

    if ($stream->peek()?->token === Token::ParameterValue) {
      $value = $stream->consume()->value;
    }

    return new Parameter($name, $value);
  }

  /**
   * Parse an optional parameter list from the supplied stream.
   *
   * If no parameter list is present, this method will produce zero parameters.
   *
   * @param \ClayFreeman\LinkHeaderParser\LexemeStream $stream
   *   The lexeme stream from which to parse an optional parameter list.
   *
   * @return \ClayFreeman\LinkHeaderParser\Parser\Value\Parameter[]
   *   The parsed parameter list.
   */
  protected function parseParameters(LexemeStream $stream): array {
    $parameters = [];

    while ($stream->peek()?->token === Token::ParameterName) {
      $parameter = $this->parseParameter($stream);
      $parameters[\strtolower($parameter->name)] = $parameter;
    }

    return $parameters;
  }

}
