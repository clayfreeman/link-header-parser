<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser\Parser\Value;

/**
 * Represents a single HTTP 'Link' header parameter.
 */
class Parameter {

  /**
   * Constructs a Parameter object.
   *
   * @param string $name
   *   The name of the parameter.
   * @param string $value
   *   The value of the parameter.
   */
  public function __construct(public readonly string $name, public readonly string $value = '') {
  }

}
