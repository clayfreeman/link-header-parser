<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser;

/**
 * Represents a single lexeme.
 */
class Lexeme {

  /**
   * The value represented by the lexeme.
   *
   * @var mixed
   */
  public readonly mixed $value;

  /**
   * Constructs a Lexeme object.
   *
   * @param \ClayFreeman\LinkHeaderParser\Token $token
   *   The token of which the supplied value is an instance.
   * @param mixed $value
   *   The value represented by the lexeme.
   */
  public function __construct(public readonly Token $token, $value = NULL) {
    $this->value = $value;
  }

}
